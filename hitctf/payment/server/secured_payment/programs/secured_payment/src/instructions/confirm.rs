use anchor_lang::prelude::*;

use crate::state::*;
use crate::error::*;

#[derive(Accounts)]
pub struct Confirm<'info> {
    #[account(mut)]
    pub from: Signer<'info>,
    #[account(
        seeds = [b"config".as_ref()],
        bump = config.bump,
        constraint = config.allow_early_confirm @ SecuredPaymentError::EarlyConfirmNotAllowed
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
        constraint = payment.from.eq(from.key) @ SecuredPaymentError::IncorrectFromAccount,
        constraint = payment.status == PaymentStatus::Locked @ SecuredPaymentError::PaymentNotLocked
    )]
    pub payment: Account<'info, Payment>,
    pub system_program: Program<'info, System>,
}

pub fn confirm_handler(ctx: Context<Confirm>) -> Result<()> {

    msg!("payment {} confirmed by {}",
        ctx.accounts.payment.key(),
        ctx.accounts.from.key()
    );

    ctx.accounts.payment.status = PaymentStatus::Unlocked;
    ctx.accounts.customer_record.confirm_cnt = 
        ctx.accounts.customer_record.confirm_cnt.saturating_add(1);
    
    msg!("{} confirmed, confirm_cnt: {}", ctx.accounts.payment.key(), ctx.accounts.customer_record.confirm_cnt);

    Ok(())
}