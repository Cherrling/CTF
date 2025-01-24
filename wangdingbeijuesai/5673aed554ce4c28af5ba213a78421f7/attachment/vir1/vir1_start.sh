#!/bin/bash

exec qemu-system-x86_64 \
	-m 64M \
	-kernel ./bzImage \
	-initrd ./rootfs.cpio \
	-append "console=ttyS0 kaslr quiet" \
	-cpu kvm64,+smap,smep\
	-smp cores=1,threads=1 \
	-nographic \
	-no-reboot
