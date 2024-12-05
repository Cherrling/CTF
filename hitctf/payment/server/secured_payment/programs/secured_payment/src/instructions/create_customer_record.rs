use anchor_lang::prelude::*;

use crate::state::*;

#[derive(Accounts)]
pub struct CreateCustomerRecord<'info> {
    #[account(mut)]
    pub owner: Signer<'info>,
    #[account(
        seeds = [b"config".as_ref()],
        bump = config.bump,
    )]
    pub config: Account<'info, Config>,
    #[account(
        init,
        payer = owner,
        space = 8 + CustomerRecord::INIT_SPACE,
        seeds = [b"customer_record".as_ref(), owner.key().as_ref()],
        bump
    )]
    pub customer_record: Account<'info, CustomerRecord>,
    #[account(
        init,
        payer = owner,
        space = 8 + EmptyAccount::INIT_SPACE,
        seeds = [b"custody_account".as_ref(), owner.key().as_ref()],
        bump
    )]
    pub custody_account: Account<'info, EmptyAccount>,
    pub system_program: Program<'info, System>,
}

pub fn create_customer_record_handler(ctx: Context<CreateCustomerRecord>) -> Result<()> {
    ctx.accounts.customer_record.set_inner(CustomerRecord {
        total_amount: 0,
        refund_amount: 0,
        paid_amount: 0,
        owner: ctx.accounts.owner.key(),
        custody_account: ctx.accounts.custody_account.key(),
        payment_cnt: 0,
        confirm_cnt: 0,
        refund_cnt: 0,
        pending_payment_cnt: 0,
        bump: ctx.bumps.customer_record
    });

    ctx.accounts.custody_account.set_inner(EmptyAccount {
        bump: ctx.bumps.custody_account
    });

    msg!("CustomerRecord for {} created on {} with custody account {}",
        ctx.accounts.owner.key(),
        ctx.accounts.customer_record.key(),
        ctx.accounts.custody_account.key()
    );

    Ok(())
}