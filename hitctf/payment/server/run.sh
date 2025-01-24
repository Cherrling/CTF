#!/bin/bash
pushd secured_payment
anchor build
popd
cargo build -r
./target/release/server

