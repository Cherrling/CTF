pub use anchor_lang;

use anchor_lang::prelude::*;

pub mod instructions;
use instructions::*;

pub mod state;
pub mod error;

declare_id!("4Y2Xg2yiHNCzEvfsz9DjGBzhCinu3z7R6pG3RbDqVgUd");

#[program]
pub mod secured_payment {
    use super::*;

    pub fn initialize(ctx: Context<Initialize>, args: InitializeArgs) -> Result<()> {
        initialize_handler(ctx, args)
    }
    pub fn create_customer_record(ctx: Context<CreateCustomerRecord>) -> Result<()> {
        create_customer_record_handler(ctx)
    }
    pub fn close_customer_record(ctx: Context<CloseCustomerRecord>) -> Result<()> {
        close_customer_record_handler(ctx)
    }
    pub fn pay(ctx: Context<Pay>, args: PayArgs) -> Result<()> {
        pay_handler(ctx, args)
    }
    pub fn claim(ctx: Context<Claim>) -> Result<Vec<u8>> {
        claim_handler(ctx)
    }
    pub fn claim_batch<'info>(ctx: Context<'_, '_, 'info, 'info, ClaimBatch<'info>>, cnt: u8) -> Result<()> {
        claim_batch_handler(ctx, cnt)
    }
    pub fn confirm(ctx: Context<Confirm>) -> Result<()> {
        confirm_handler(ctx)
    }
    pub fn refund(ctx: Context<Refund>, dispute_refund: bool) -> Result<()> {
        refund_handler(ctx, dispute_refund)
    }
}


