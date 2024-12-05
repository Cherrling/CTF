use anchor_lang::prelude::*;
use secured_payment::program::SecuredPayment;

declare_id!("BzyxyvKY5JybTPWZ3a91FyAXpPrjJUXvyNgHASkP7vHD");

#[program]
pub mod solve {
    use super::*;

    pub fn solve(ctx: Context<Solve>) -> Result<()> {
        msg!("Greetings from: {:?}", ctx.program_id);
        msg!("Your sol balance: {}", ctx.accounts.user.lamports());
        Ok(())
    }
}

// you can add whatever accounts you need here, and remember to adjust solve.py as well
#[derive(Accounts)]
pub struct Solve<'info> {
    #[account(mut)]
    pub user: Signer<'info>,
    pub secured_payment: Program<'info, SecuredPayment>,
    pub system_program: Program<'info, System>,
}
