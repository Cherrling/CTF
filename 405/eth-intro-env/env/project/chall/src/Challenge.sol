// SPDX-License-Identifier: UNLICENSED
pragma solidity ^0.8.0;

contract Challenge {
    bool public solved1;
    bool public solved2;

    uint256 private secret1;
    uint256 private immutable secret2;

    constructor(uint256 _secret1, uint256 _secret2) {
        secret1 = _secret1;
        secret2 = _secret2;
    }

    function solve1(address user, uint256 password) payable external {
        require(msg.sender == user, "Who are you?");

        require(msg.value >= 0.1 ether, "Aren't you forgetting something?");

        require(password == secret1, "Password error!");

        solved1 = true;
    }

    
    function solve2(address user, uint256 password) external {
        require(tx.origin == user, "Who are you?");
        require(tx.origin != msg.sender, "No EOA!");

        require(msg.sender.code.length == 0, "No code is allowed");

        require(password == secret2, "Password error!");

        solved2 = true;
    }

    function isSolved1() external view returns (bool) {
        return solved1;
    }
    function isSolved2() external view returns (bool) {
        return solved2;
    }
}
