use anchor_lang::prelude::*;

use crate::state::*;
use crate::error::*;

#[derive(Accounts)]
pub struct ClaimBatch<'info> {
    /// CHECK: just a pubkey
    pub to: UncheckedAccount<'info>,
    /// CHECK: just a pubkey, actual sol receiver
    /// if None, then receiver will be `to`
    pub receiver: Option<UncheckedAccount<'info>>,
    #[account(
        seeds = [b"config".as_ref()],
        bump = config.bump,
    )]
    pub config: Account<'info, Config>,
    pub system_program: Program<'info, System>,
}

pub fn claim_batch_handler<'info>(_ctx: Context<'_, '_, 'info, 'info, ClaimBatch<'info>>, _cnt: u8) -> Result<()> {
    err!(SecuredPaymentError::Unimplemented)
}
