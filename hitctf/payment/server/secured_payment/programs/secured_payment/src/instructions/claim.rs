use anchor_lang::prelude::*;

use crate::state::*;
use crate::error::*;

#[derive(Accounts)]
pub struct Claim<'info> {
    /// CHECK: just a pubkey
    pub from: UncheckedAccount<'info>,
    /// CHECK: just a pubkey
    #[account(mut)]
    pub to: UncheckedAccount<'info>,
    /// CHECK: just a pubkey, actual sol receiver
    /// if None, then receiver will be `to`
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
        has_one = to @ SecuredPaymentError::IncorrectToAccount
    )]
    pub payment: Account<'info, Payment>,
    pub system_program: Program<'info, System>,
}

pub fn claim_handler(ctx: Context<Claim>) -> Result<Vec<u8>> {

    msg!("Try to claim payment {}", ctx.accounts.payment.key());

    let actual_receiver = match &ctx.accounts.receiver {
        Some(receiver) => {
            require!(ctx.accounts.to.is_signer, SecuredPaymentError::RequireToSigner);
            receiver
        },
        None => &ctx.accounts.to
    };
    msg!("Actual receiver {}", actual_receiver.key());

    checked_claim(
        &ctx.accounts.payment.to_account_info(),
        &ctx.accounts.customer_record.to_account_info(),
        &ctx.accounts.to.to_account_info(),
        &ctx.accounts.from.to_account_info(),
        &actual_receiver.to_account_info(),
        &ctx.accounts.custody_account.to_account_info()
    )
}

pub fn checked_claim<'info>(
    payment_info: &AccountInfo<'info>,
    customer_record_info: &AccountInfo<'info>,
    to: &AccountInfo<'info>,
    from: &AccountInfo<'info>,
    receiver: &AccountInfo<'info>,
    custody_account_info: &AccountInfo<'info>
)  -> Result<Vec<u8>> {

    // validate and deserialize payment account
    
    let mut payment = {
        let buf = &mut &payment_info.try_borrow_data()?[8..];
        Payment::deserialize(buf)?
    };

    msg!("payment: {} {:?}", payment_info.key, payment.status);

    require_keys_eq!(*payment_info.owner, crate::id(), SecuredPaymentError::AccountOwnerMismatch);
    require_keys_eq!(from.key(), payment.from, SecuredPaymentError::IncorrectFromAccount);
    require_keys_eq!(to.key(), payment.to, SecuredPaymentError::IncorrectToAccount);

    // validate customer_record account
    let mut customer_record = {
        let buf = &mut &customer_record_info.try_borrow_data()?[8..];
        CustomerRecord::deserialize(buf)?
    };

    let (expected_customer_record, customer_record_bump) = Pubkey::find_program_address(
        &[
            b"customer_record".as_ref(),
            from.key().as_ref()
        ],
        &crate::id()
    );
    require_keys_eq!(*customer_record_info.owner, crate::id(), SecuredPaymentError::AccountOwnerMismatch);
    require_keys_eq!(expected_customer_record, customer_record_info.key(), SecuredPaymentError::IncorrectPDA);
    require_eq!(customer_record.bump, customer_record_bump, SecuredPaymentError::BumpMismatch);

    // validate custody_account

    let custody_account = {
        let buf = &mut &custody_account_info.try_borrow_data()?[8..];
        EmptyAccount::deserialize(buf)?
    };

    let (expected_custody_account, custody_account_bump) = Pubkey::find_program_address(
        &[
            b"custody_account".as_ref(),
            from.key().as_ref()
        ],
        &crate::id()
    );
    require_keys_eq!(*custody_account_info.owner, crate::id(), SecuredPaymentError::AccountOwnerMismatch);
    require_keys_eq!(expected_custody_account, custody_account_info.key(), SecuredPaymentError::IncorrectPDA);
    require_eq!(custody_account.bump, custody_account_bump, SecuredPaymentError::BumpMismatch);

    // check unlock

    match payment.status {
        PaymentStatus::Locked => {
            require_gte!(
                Clock::get()?.unix_timestamp,
                payment.unlock_time,
                SecuredPaymentError::PaymentNotUnlocked
            )
        }
        PaymentStatus::Claimed => {
            err!(SecuredPaymentError::PaymentAlreadyClaimed)?
        }
        PaymentStatus::Refunded => {
            err!(SecuredPaymentError::PaymentAlreadyRefunded)?
        }
        PaymentStatus::Unlocked => {}
    }

    custody_account_info.sub_lamports(payment.amount)?;
    receiver.add_lamports(payment.amount)?;

    // update account data
    customer_record.paid_amount =
        customer_record.paid_amount
        .saturating_sub(payment.amount);

    customer_record.pending_payment_cnt = 
        customer_record.pending_payment_cnt
        .checked_sub(1)
        .ok_or(SecuredPaymentError::PendingPaymentCntOverflow)?;

    // mark as claimed
    payment.status = PaymentStatus::Claimed;

    // write back
    {
        payment.serialize(&mut &mut payment_info.try_borrow_mut_data()?[8..])?;
        customer_record.serialize(&mut &mut customer_record_info.try_borrow_mut_data()?[8..])?;
    }

    Ok(payment.message)
}