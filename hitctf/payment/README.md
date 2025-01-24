## Structure

- server: including the judge server and the secured_payment solana program
- client: including a solana program example and a python script for server interaction, provided for reference only. You are free to modify them as needed to suit your requirements.
- docker_env: identical to the remote docker environment except that the real flag is not included. It can be used for local verification.

### docker_env

Dockerfile only contains the final compiled artifacts, not including the compiling process, so we provide the compilation environment details.

All artifacts are built on `Ubuntu 22.04`.
Solana programs are compiled with `Anchor 0.30.1` and `Solana 1.18.10`.
Server is compiled with `rustc 1.80.1 (3f5fd8dd4 2024-08-06)`.

Compile command lines:
```bash
pushd server
cargo build -r
popd
pushd server/secured_payment
anchor build
popd
```
