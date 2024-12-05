use anchor_lang::prelude::*;

use crate::state::*;
use crate::error::*;

#[derive(Accounts)]
pub struct Refund<'info> {
    /// CHECK: just a pubkey
    #[account(mut)]
    pub from: UncheckedAccount<'info>,
    /// CHECK: just a pubkey
    pub to: UncheckedAccount<'info>,
    #[account(
        address = config.admin @ SecuredPaymentError::IncorrectAdmin
    )]
    pub admin: Option<Signer<'info>>,
    /// CHECK: just a pubkey, actual sol receiver
    /// if None, then receiver will be `from`
    #[account(mut)]
    pub receiver: Option<UncheckedAccount<'info>>,
    #[account(
        seeds = [b"config".as_ref()],
        bump = config.bump,
    )]
    pub config: Account<'info, Config>,
    #[account(
        mut,
        seeds = [b"customer_record".as_ref(), from.key().as_ref()],
        bump = customer_record.bump,
        constraint = customer_record.owner.eq(from.key) @ SecuredPaymentError::IncorrectFromAccount
    )]
    pub customer_record: Account<'info, CustomerRecord>,
    #[account(
        mut,
        seeds = [b"custody_account".as_ref(), from.key().as_ref()],
        bump = custody_account.bump
    )]
    pub custody_account: Account<'info, EmptyAccount>,
    #[account(
        mut,
        has_one = from @ SecuredPaymentError::IncorrectFromAccount,
        has_one = to @ SecuredPaymentError::IncorrectToAccount,
        constraint = payment.status == PaymentStatus::Locked @ SecuredPaymentError::PaymentNotLocked
    )]
    pub payment: Account<'info, Payment>,
    pub system_program: Program<'info, System>,
}

pub fn refund_handler(ctx: Context<Refund>, dispute_refund: bool) -> Result<()> {

    msg!("Initiated a refund for payment {}", ctx.accounts.payment.key());

    let amount = ctx.accounts.payment.amount;

    if dispute_refund {
        // only admin can initiate a disputed refund
        require!(ctx.accounts.admin.is_some(), SecuredPaymentError::RequireAdminSigner);
    } else {
        // to can also initiate a normal refund
        require!(ctx.accounts.to.is_signer, SecuredPaymentError::RequireToSigner);
    }

    let actual_receiver = match &ctx.accounts.receiver {
        Some(receiver) => {
            require!(ctx.accounts.from.is_signer, SecuredPaymentError::RequireFromSigner);
            receiver.to_account_info()
        },
        None => ctx.accounts.from.to_account_info()
    };
    msg!("Actual receiver {}", actual_receiver.key());

    ctx.accounts.custody_account.sub_lamports(amount)?;
    actual_receiver.add_lamports(amount)?;

    ctx.accounts.customer_record.refund_amount =
        ctx.accounts.customer_record.refund_amount
        .saturating_add(amount);

    ctx.accounts.customer_record.refund_cnt =
        ctx.accounts.customer_record.refund_cnt
        .saturating_add(1);

    ctx.accounts.customer_record.pending_payment_cnt = 
        ctx.accounts.customer_record.pending_payment_cnt
        .checked_sub(1)
        .ok_or(SecuredPaymentError::PendingPaymentCntOverflow)?;

    ctx.accounts.payment.status = PaymentStatus::Refunded;
    Ok(())
}