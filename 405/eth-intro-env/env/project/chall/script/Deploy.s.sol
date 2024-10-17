// SPDX-License-Identifier: UNLICENSED
pragma solidity ^0.8.0;

import "forge-ctf/CTFDeployer.sol";

import "src/Challenge.sol";

contract Deploy is CTFDeployer {
    function deploy(address system, address player) internal override returns (address challenge) {
        vm.startBroadcast(system);
        uint256 secret1 = vm.envUint("secret1");
        uint256 secret2 = vm.envUint("secret2");

        challenge = address(new Challenge(secret1, secret2));

        vm.stopBroadcast();
    }
}
