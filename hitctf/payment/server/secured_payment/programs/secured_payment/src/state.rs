use anchor_lang::prelude::*;


#[account]
#[derive(PartialEq, InitSpace, Debug)]
pub struct Config {
    pub admin: Pubkey,
    pub min_locking_duration: u64,
    pub max_locking_duration: u64,
    pub allow_early_confirm: bool,
    pub bump: u8
}

#[account]
#[derive(PartialEq, InitSpace, Debug)]
pub struct CustomerRecord {
    pub total_amount: u64,
    pub refund_amount: u64,
    pub paid_amount: u64,
    pub owner: Pubkey,
    pub custody_account: Pubkey,
    pub payment_cnt: u32,
    pub confirm_cnt: u32,
    pub refund_cnt: u32,
    pub pending_payment_cnt: u32,
    pub bump: u8,
}

#[derive(AnchorSerialize, AnchorDeserialize, Clone, Copy, PartialEq, InitSpace, Debug)]
pub enum PaymentStatus {
    Locked,
    Unlocked,
    Claimed,
    Refunded
}

#[account]
#[derive(PartialEq, InitSpace, Debug)]
pub struct Payment {
    #[max_len(16)]
    pub message: Vec<u8>,
    pub status: PaymentStatus,
    pub unlock_time: i64,
    pub from: Pubkey,
    pub to: Pubkey,
    pub amount: u64,
}


#[account]
#[derive(PartialEq, InitSpace, Debug)]
pub struct EmptyAccount {
    pub bump: u8,
}
