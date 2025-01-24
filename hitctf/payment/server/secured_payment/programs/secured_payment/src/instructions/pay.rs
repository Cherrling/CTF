use anchor_lang::{prelude::*, system_program::{self, Transfer}};

use crate::state::*;
use crate::error::*;

#[derive(Accounts)]
pub struct Pay<'info> {
    /// CHECK: only a pubkey, we don't care who actually pay
    pub from: UncheckedAccount<'info>,
    /// CHECK: only a pubkey
    pub to: UncheckedAccount<'info>,
    #[account(mut)]
    pub payer: Signer<'info>,
    #[account(
        seeds = [b"config".as_ref()],
        bump = config.bump,
    )]
    pub config: Account<'info, Config>,
    #[account(
        mut,
        seeds = [b"customer_record".as_ref(), from.key().as_ref()],
        bump = customer_record.bump,
    )]
    pub customer_record: Account<'info, CustomerRecord>,
    #[account(
        mut,
        seeds = [b"custody_account".as_ref(), from.key().as_ref()],
        bump
    )]
    pub custody_account: Account<'info, EmptyAccount>,
    #[account(
        init,
        payer = payer,
        space = 8 + Payment::INIT_SPACE
    )]
    pub payment: Account<'info, Payment>,
    pub system_program: Program<'info, System>,
}

#[derive(AnchorSerialize, AnchorDeserialize, Clone, Copy, PartialEq)]
pub struct PayArgs {
    pub amount: u64,
    pub message: [u8; 16],
    pub lock_duration: Option<u64>,
}

pub fn pay_handler(ctx: Context<Pay>, args: PayArgs) -> Result<()> {

    msg!("Try to create payment on {}", ctx.accounts.payment.key());

    system_program::transfer(
        CpiContext::new(
            ctx.accounts.system_program.to_account_info(),
            Transfer {
                from: ctx.accounts.payer.to_account_info(),
                to: ctx.accounts.custody_account.to_account_info(),
            }
        ),
        args.amount
    )?;

    // just for statistics, allow overflow
    ctx.accounts.customer_record.total_amount = 
        ctx.accounts.customer_record.total_amount
        .saturating_add(args.amount);

    ctx.accounts.customer_record.payment_cnt = 
        ctx.accounts.customer_record.payment_cnt
        .saturating_add(1);

    ctx.accounts.customer_record.pending_payment_cnt = 
        ctx.accounts.customer_record.pending_payment_cnt
        .checked_add(1)
        .ok_or(SecuredPaymentError::PendingPaymentCntOverflow)?;

    let lock_duration = args.lock_duration.unwrap_or(0u64);

    require_gte!(
        lock_duration,
        ctx.accounts.config.min_locking_duration,
        SecuredPaymentError::LockDurationTooShort
    );

    require_gte!(
        ctx.accounts.config.max_locking_duration,
        lock_duration,
        SecuredPaymentError::LockDurationTooLong
    );

    let unlock_time =
        Clock::get()?.unix_timestamp
        .checked_add(
            lock_duration.try_into()?
        ).ok_or(SecuredPaymentError::LockTimeOverflow)?;

    ctx.accounts.payment.set_inner(Payment {
        message: args.message.to_vec(),
        status: PaymentStatus::Locked,
        amount: args.amount,
        unlock_time,
        from: ctx.accounts.from.key(),
        to: ctx.accounts.to.key(),
    });

    msg!("Payment created {:?}", ctx.accounts.payment.clone().into_inner());

    Ok(())
}