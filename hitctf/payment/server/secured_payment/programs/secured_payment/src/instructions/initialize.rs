use anchor_lang::prelude::*;

use crate::state::Config;
use crate::error::*;

#[derive(Accounts)]
pub struct Initialize<'info> {
    #[account(mut)]
    pub admin: Signer<'info>,
    #[account(
        init,
        seeds = [b"config".as_ref()],
        bump,
        payer = admin,
        space = 8 + Config::INIT_SPACE
    )]
    pub config: Account<'info, Config>,
    pub system_program: Program<'info, System>,
}

#[derive(AnchorSerialize, AnchorDeserialize, Clone, Copy, PartialEq)]
pub struct InitializeArgs {
    pub min_locking_duration: u64,
    pub max_locking_duration: u64,
    pub allow_early_confirm: bool
}

pub fn initialize_handler(ctx: Context<Initialize>, args: InitializeArgs) -> Result<()> {

    let config = &mut ctx.accounts.config;

    require_gte!(args.max_locking_duration, args.min_locking_duration, SecuredPaymentError::InvalidInitializeArgs);

    config.set_inner(Config {
        admin: ctx.accounts.admin.key(),
        min_locking_duration: args.min_locking_duration,
        max_locking_duration: args.max_locking_duration,
        allow_early_confirm: args.allow_early_confirm,
        bump: ctx.bumps.config
    });

    msg!("Config initialized on {} with bump {}",
        ctx.accounts.config.key(),
        ctx.bumps.config
    );

    Ok(())
}