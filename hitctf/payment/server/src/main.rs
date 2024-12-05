use sol_ctf_framework::ChallengeBuilder;

use solana_program::{
    instruction::Instruction,
    pubkey::Pubkey,
    system_instruction, system_program,
};
use solana_program_test::tokio;
use solana_sdk::native_token::sol_to_lamports;
use solana_sdk::{signature::Signer, signer::keypair::Keypair};
use solana_sdk::compute_budget::ComputeBudgetInstruction;
use std::error::Error;
use std::io::Write;
use std::net::{TcpListener, TcpStream};

use secured_payment::anchor_lang::{InstructionData, ToAccountMetas};

#[tokio::main]
async fn main() -> Result<(), Box<dyn Error>> {
    let listener = TcpListener::bind("0.0.0.0:1337")?;

    println!("Listening on port 1337 ...");

    for stream in listener.incoming() {
        let stream = stream.unwrap();

        tokio::spawn(async {
            if let Err(err) = handle_connection(stream).await {
                println!("error: {:?}", err);
            }
        });
    }
    Ok(())
}

async fn handle_connection(mut socket: TcpStream) -> Result<(), Box<dyn Error>> {
    let mut builder = ChallengeBuilder::try_from(socket.try_clone().unwrap()).unwrap();

    let program_id = builder.add_program("secured_payment/target/deploy/secured_payment.so", Some(secured_payment::ID));
    let solve_id = builder.input_program()?;

    let mut chall = builder.build().await;

    let payer_keypair = &chall.ctx.payer;
    let payer = payer_keypair.pubkey();

    // Creating admin keypair
    let admin_keypair = Keypair::new();
    let admin = admin_keypair.pubkey();

    chall
        .run_ix(system_instruction::transfer(
            &payer,
            &admin,
            sol_to_lamports(100.1),
        ))
        .await?;

    // Creating user keypair
    let user_keypair = Keypair::new();
    let user = user_keypair.pubkey();

    chall
        .run_ix(system_instruction::transfer(
            &payer,
            &user,
            sol_to_lamports(10.1)
        ))
        .await?;

    let mut ixs = vec![];

    // initialize
    let config = Pubkey::find_program_address(&[b"config"], &program_id).0;
    ixs.push(Instruction::new_with_bytes(
        program_id,
        &secured_payment::instruction::Initialize {
            args: secured_payment::instructions::InitializeArgs {
                min_locking_duration: 0,
                max_locking_duration: 3600 * 24 * 365 * 10,
                allow_early_confirm: true
            }
        }.data(),
        secured_payment::accounts::Initialize {
            admin: admin,
            config: config,
            system_program: system_program::ID
        }.to_account_metas(None)
    ));
    // create CustomerRecord
    let customer_record = Pubkey::find_program_address(&[b"customer_record", admin.as_ref()], &program_id).0;
    let custody_account = Pubkey::find_program_address(&[b"custody_account", admin.as_ref()], &program_id).0;
    ixs.push(Instruction::new_with_bytes(
        program_id,
        &secured_payment::instruction::CreateCustomerRecord {}.data(),
        secured_payment::accounts::CreateCustomerRecord {
            owner: admin,
            config: config,
            customer_record: customer_record,
            custody_account: custody_account,
            system_program: system_program::ID
        }.to_account_metas(None)
    ));
    // pay 100 sol to user after 1 year
    let payment_keypair = Keypair::new();
    ixs.push(Instruction::new_with_bytes(
        program_id,
        &secured_payment::instruction::Pay {
            args: secured_payment::instructions::PayArgs {
                amount: sol_to_lamports(100.0),
                message: *b"get sol 1y later",
                lock_duration: Some(3600 * 24 * 365)
            }
        }.data(),
        secured_payment::accounts::Pay {
            from: admin,
            to: user,
            payer: admin,
            config: config,
            customer_record: customer_record,
            custody_account: custody_account,
            payment: payment_keypair.pubkey(),
            system_program: system_program::ID
        }.to_account_metas(None)
    ));

    chall.run_ixs_full(&ixs[..], &[&admin_keypair, &payment_keypair], &admin).await?;

    writeln!(socket, "program: {}", program_id)?;
    writeln!(socket, "user: {}", user)?;
    writeln!(socket, "admin: {}", admin)?;
    writeln!(socket, "payment: {}", payment_keypair.pubkey())?;

    let bump_budget = ComputeBudgetInstruction::set_compute_unit_limit(10_000_000);
    let solve_ix = chall.read_instruction(solve_id)?;

    chall
        .run_ixs_full(&[bump_budget, solve_ix], &[&user_keypair], &user_keypair.pubkey())
        .await?;

    if let Some(account) = chall.ctx.banks_client.get_account(user).await? {
        if account.lamports > sol_to_lamports(110.0) {
            writeln!(
                socket,
                "Good job!\n{}",
                std::env::var("FLAG").unwrap_or_else(|_| "flag{test_flag}".to_string())
            )?;
        } else {
            writeln!(socket, "Try harder")?;
        }
    } else {
        writeln!(socket, "Try harder!")?;
    }
    Ok(())
}
