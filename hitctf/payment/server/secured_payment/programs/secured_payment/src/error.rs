use anchor_lang::prelude::*;

#[error_code]
pub enum  SecuredPaymentError {
    #[msg("Invalid initialize arguments")]
    InvalidInitializeArgs,
    #[msg("Incorrect Admin Account")]
    IncorrectAdmin,
    #[msg("Incorrect PDA")]
    IncorrectPDA,
    #[msg("Bump mismatch")]
    BumpMismatch,
    #[msg("Incorrect owner")]
    IncorrectOwner,
    #[msg("lock_time overflow")]
    LockTimeOverflow,
    #[msg("Payment is not unlocked yet")]
    PaymentNotUnlocked,
    #[msg("Payment is not in locked state")]
    PaymentNotLocked,
    #[msg("Payment is in locked state")]
    PaymentLocked,
    #[msg("Payment is already claimed")]
    PaymentAlreadyClaimed,
    #[msg("Payment is already refunded")]
    PaymentAlreadyRefunded,
    #[msg("Still has payments not resolved")]
    PendingPaymentUnresolved,
    #[msg("Incorrect `to` account")]
    IncorrectToAccount,
    #[msg("Incorrect `from` account")]
    IncorrectFromAccount,
    #[msg("Lock duration too short")]
    LockDurationTooShort,
    #[msg("Lock duration too long")]
    LockDurationTooLong,
    #[msg("Require `to` to be a signer")]
    RequireToSigner,
    #[msg("Require `from` to be a signer")]
    RequireFromSigner,
    #[msg("Require `admin` to be a signer")]
    RequireAdminSigner,
    #[msg("pending_payment_cnt overflow")]
    PendingPaymentCntOverflow,
    #[msg("Account owner mismatch")]
    AccountOwnerMismatch,
    #[msg("No enough remaining_accounts")]
    NoEnoughRemainingAccounts,
    #[msg("Unimplemented")]
    Unimplemented,
    #[msg("Early confirm is not allowed")]
    EarlyConfirmNotAllowed,
}