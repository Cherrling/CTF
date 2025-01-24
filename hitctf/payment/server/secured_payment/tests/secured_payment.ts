import * as anchor from "@coral-xyz/anchor";
import { Program } from "@coral-xyz/anchor";
import { SecuredPayment } from "../target/types/secured_payment";
import {
  SystemProgram,
  Keypair,
  Transaction,
  sendAndConfirmTransaction,
  Connection,
  clusterApiUrl,
  LAMPORTS_PER_SOL,
  ComputeBudgetProgram,
} from "@solana/web3.js";

describe("secured_payment", () => {
  // Configure the client to use the local cluster.
  let provider = anchor.AnchorProvider.env();
  anchor.setProvider(provider);

  const program = anchor.workspace.SecuredPayment as Program<SecuredPayment>;

  let [config] = anchor.web3.PublicKey.findProgramAddressSync(
    [Buffer.from("config")],
    program.programId
  );

  const admin = anchor.web3.Keypair.generate();
  const user = anchor.web3.Keypair.generate();

  it("initialize program state", async () => {
    await provider.connection.confirmTransaction(
      await provider.connection.requestAirdrop(
        admin.publicKey,
        10000000000
      ),
      "confirmed"
    );
    await provider.connection.confirmTransaction(
      await provider.connection.requestAirdrop(
        user.publicKey,
        10000000000
      ),
      "confirmed"
    );
  });
  it("initialize", async () => {
    const tx = await program.methods.initialize({
        minLockingDuration: new anchor.BN(0),
        maxLockingDuration: new anchor.BN(3600 * 24 * 365),
        allowEarlyConfirm: true
      }
    )
      .accounts({
        admin: admin.publicKey
      })
      .signers([admin])
      .rpc();
    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    const txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    const logs = txDetails?.meta?.logMessages || null;
    console.log(logs);
  });

  it("create_customer_record", async () => {
    const tx = await program.methods.createCustomerRecord()
      .accounts({
        owner: admin.publicKey
      })
      .signers([admin])
      .rpc();
    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    const txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    const logs = txDetails?.meta?.logMessages || null;
    console.log(logs);
  });

  it("pay and claim test", async () => {
    // payment: from `admin` to `user`, 1000
    const payment = anchor.web3.Keypair.generate();
    let tx = await program.methods.pay({
      amount: new anchor.BN(1000),
      message: Array.from(new TextEncoder().encode("hello")),
      lockDuration: new anchor.BN(0),
    }).accounts({
      from: admin.publicKey,
      to: user.publicKey,
      payer: admin.publicKey,
      payment: payment.publicKey
    }).signers([admin, payment]).rpc();

    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    let txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    let logs = txDetails?.meta?.logMessages || null;
    console.log(logs);

    // try to claim payment
    tx = await program.methods.claim().accounts({
      payment: payment.publicKey,
      receiver: null
    }).rpc();

    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    logs = txDetails?.meta?.logMessages || null;
    console.log(logs);
  });
  
  it("pay, confirm and claim test", async () => {
    // payment: from `admin` to `user`, 1000000, lock 3600 * 24 * 265
    const payment = anchor.web3.Keypair.generate();
    let tx = await program.methods.pay({
      amount: new anchor.BN(1000000),
      message: Array.from(new TextEncoder().encode("hello")),
      lockDuration: new anchor.BN(3600 * 24 * 265),
    }).accounts({
      from: admin.publicKey,
      to: user.publicKey,
      payer: admin.publicKey,
      payment: payment.publicKey
    }).signers([admin, payment]).rpc();

    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    let txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    let logs = txDetails?.meta?.logMessages || null;
    console.log(logs);

    // try to claim payment, will failed due to not unlocked
    let err = false;
    try {
      tx = await program.methods.claim().accounts({
        payment: payment.publicKey,
        receiver: null
      }).rpc();

      console.log("Your transaction signature", tx);
      await program.provider.connection.confirmTransaction(tx, "confirmed");
      txDetails = await program.provider.connection.getTransaction(tx, {
        maxSupportedTransactionVersion: 0,
        commitment: "confirmed",
      });
      logs = txDetails?.meta?.logMessages || null;
      console.log(logs);
    } catch(e) {
      console.log({
        errorLogs: e.errorLogs,
        logs: e.logs
      });
      err = true;
    }
    if (!err) {
      throw new Error("payment claim should failed due to unlock time limit!")
    }

    // `from` try to confirm
    tx = await program.methods.confirm().accounts({
      from: admin.publicKey,
      payment: payment.publicKey,
    }).signers([admin]).rpc();

    console.log("Your transaction signature", tx);
    await program.provider.connection.confirmTransaction(tx, "confirmed");
    txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    logs = txDetails?.meta?.logMessages || null;
    console.log(logs);

    // try to claim again
    tx = await program.methods.claim().accounts({
      payment: payment.publicKey,
      receiver: null
    }).rpc();

    console.log("Your transaction signature", tx);
    await program.provider.connection.confirmTransaction(tx, "confirmed");
    txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    logs = txDetails?.meta?.logMessages || null;
    console.log(logs);
    
  })

  it("pay and refund by `to` test", async () => {
    // payment: from `admin` to `user`, 1000
    const payment = anchor.web3.Keypair.generate();
    let tx = await program.methods.pay({
      amount: new anchor.BN(1000),
      message: Array.from(new TextEncoder().encode("hello")),
      lockDuration: new anchor.BN(0),
    }).accounts({
      from: admin.publicKey,
      to: user.publicKey,
      payer: admin.publicKey,
      payment: payment.publicKey
    }).signers([admin, payment]).rpc();

    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    let txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    let logs = txDetails?.meta?.logMessages || null;
    console.log(logs);

    // try refund payment
    let raw_tx = await program.methods.refund(false).accounts({
      payment: payment.publicKey,
      receiver: null,
      admin: null
    }).transaction();

    // change `to` to signer
    raw_tx.instructions[0].keys[1].isSigner = true

    tx = await program.provider.connection.sendTransaction(
      raw_tx, [user]
    )
  
    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    logs = txDetails?.meta?.logMessages || null;
    console.log(logs);
  })

  it("pay and refund by `admin` test", async () => {
    // payment: from `admin` to `user`, 1000
    const payment = anchor.web3.Keypair.generate();
    let tx = await program.methods.pay({
      amount: new anchor.BN(1000),
      message: Array.from(new TextEncoder().encode("hello")),
      lockDuration: new anchor.BN(0),
    }).accounts({
      from: admin.publicKey,
      to: user.publicKey,
      payer: admin.publicKey,
      payment: payment.publicKey
    }).signers([admin, payment]).rpc();

    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    let txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    let logs = txDetails?.meta?.logMessages || null;
    console.log(logs);

    // refund payment by admin
    tx = await program.methods.refund(true).accounts({
      payment: payment.publicKey,
      receiver: null,
      admin: admin.publicKey
    }).signers([admin]).rpc();

    console.log("Your transaction signature", tx);

    await program.provider.connection.confirmTransaction(tx, "confirmed");
    txDetails = await program.provider.connection.getTransaction(tx, {
      maxSupportedTransactionVersion: 0,
      commitment: "confirmed",
    });
    logs = txDetails?.meta?.logMessages || null;
    console.log(logs);
  });
});
