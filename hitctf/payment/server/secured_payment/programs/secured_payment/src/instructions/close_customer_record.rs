use anchor_lang::prelude::*;

use crate::state::*;
use crate::error::*;

#[derive(Accounts)]
pub struct CloseCustomerRecord<'info> {
    #[account(mut)]
    pub owner: Signer<'info>,
    #[account(
        seeds = [b"config".as_ref()],
        bump = config.bump,
    )]
    pub config: Account<'info, Config>,
    #[account(
        mut,
        seeds = [b"customer_record".as_ref(), owner.key().as_ref()],
        bump = customer_record.bump,
        close = owner
    )]
    pub customer_record: Account<'info, CustomerRecord>,
    #[account(
        mut,
        seeds = [b"custody_account".as_ref(), owner.key().as_ref()],
        bump = custody_account.bump,
        close = owner
    )]
    pub custody_account: Account<'info, EmptyAccount>,
    pub system_program: Program<'info, System>,
}

pub fn close_customer_record_handler(_ctx: Context<CloseCustomerRecord>) -> Result<()> {
    err!(SecuredPaymentError::Unimplemented)
}