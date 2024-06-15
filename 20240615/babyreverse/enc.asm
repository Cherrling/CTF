
encryptor：     文件格式 elf64-x86-64


Disassembly of section .interp:

0000000000000318 <.interp>:
 318:	2f                   	(bad)
 319:	6c                   	insb   (%dx),%es:(%rdi)
 31a:	69 62 36 34 2f 6c 64 	imul   $0x646c2f34,0x36(%rdx),%esp
 321:	2d 6c 69 6e 75       	sub    $0x756e696c,%eax
 326:	78 2d                	js     355 <puts@plt-0xcdb>
 328:	78 38                	js     362 <puts@plt-0xcce>
 32a:	36 2d 36 34 2e 73    	ss sub $0x732e3436,%eax
 330:	6f                   	outsl  %ds:(%rsi),(%dx)
 331:	2e 32 00             	cs xor (%rax),%al

Disassembly of section .note.gnu.property:

0000000000000338 <.note.gnu.property>:
 338:	04 00                	add    $0x0,%al
 33a:	00 00                	add    %al,(%rax)
 33c:	10 00                	adc    %al,(%rax)
 33e:	00 00                	add    %al,(%rax)
 340:	05 00 00 00 47       	add    $0x47000000,%eax
 345:	4e 55                	rex.WRX push %rbp
 347:	00 02                	add    %al,(%rdx)
 349:	80 00 c0             	addb   $0xc0,(%rax)
 34c:	04 00                	add    $0x0,%al
 34e:	00 00                	add    %al,(%rax)
 350:	01 00                	add    %eax,(%rax)
 352:	00 00                	add    %al,(%rax)
 354:	00 00                	add    %al,(%rax)
	...


Disassembly of section .note.gnu.build-id:

0000000000000358 <.note.gnu.build-id>:
 358:	04 00                	add    $0x0,%al
 35a:	00 00                	add    %al,(%rax)
 35c:	14 00                	adc    $0x0,%al
 35e:	00 00                	add    %al,(%rax)
 360:	03 00                	add    (%rax),%eax
 362:	00 00                	add    %al,(%rax)
 364:	47                   	rex.RXB
 365:	4e 55                	rex.WRX push %rbp
 367:	00 7f 6b             	add    %bh,0x6b(%rdi)
 36a:	93                   	xchg   %eax,%ebx
 36b:	d9 e2                	(bad)
 36d:	c0 a3 ed db 9a 0d 83 	shlb   $0x83,0xd9adbed(%rbx)
 374:	3d de e8 c1 0a       	cmp    $0xac1e8de,%eax
 379:	3f                   	(bad)
 37a:	49 3f                	rex.WB (bad)

Disassembly of section .note.ABI-tag:

000000000000037c <.note.ABI-tag>:
 37c:	04 00                	add    $0x0,%al
 37e:	00 00                	add    %al,(%rax)
 380:	10 00                	adc    %al,(%rax)
 382:	00 00                	add    %al,(%rax)
 384:	01 00                	add    %eax,(%rax)
 386:	00 00                	add    %al,(%rax)
 388:	47                   	rex.RXB
 389:	4e 55                	rex.WRX push %rbp
 38b:	00 00                	add    %al,(%rax)
 38d:	00 00                	add    %al,(%rax)
 38f:	00 03                	add    %al,(%rbx)
 391:	00 00                	add    %al,(%rax)
 393:	00 02                	add    %al,(%rdx)
 395:	00 00                	add    %al,(%rax)
 397:	00 00                	add    %al,(%rax)
 399:	00 00                	add    %al,(%rax)
	...

Disassembly of section .gnu.hash:

00000000000003a0 <.gnu.hash>:
 3a0:	02 00                	add    (%rax),%al
 3a2:	00 00                	add    %al,(%rax)
 3a4:	09 00                	or     %eax,(%rax)
 3a6:	00 00                	add    %al,(%rax)
 3a8:	01 00                	add    %eax,(%rax)
 3aa:	00 00                	add    %al,(%rax)
 3ac:	06                   	(bad)
 3ad:	00 00                	add    %al,(%rax)
 3af:	00 00                	add    %al,(%rax)
 3b1:	00 81 00 00 00 00    	add    %al,0x0(%rcx)
 3b7:	00 09                	add    %cl,(%rcx)
 3b9:	00 00                	add    %al,(%rax)
 3bb:	00 00                	add    %al,(%rax)
 3bd:	00 00                	add    %al,(%rax)
 3bf:	00 d1                	add    %dl,%cl
 3c1:	65 ce                	gs (bad)
 3c3:	6d                   	insl   (%dx),%es:(%rdi)

Disassembly of section .dynsym:

00000000000003c8 <.dynsym>:
	...
 3e0:	14 00                	adc    $0x0,%al
 3e2:	00 00                	add    %al,(%rax)
 3e4:	12 00                	adc    (%rax),%al
	...
 3f6:	00 00                	add    %al,(%rax)
 3f8:	5d                   	pop    %rbp
 3f9:	00 00                	add    %al,(%rax)
 3fb:	00 20                	add    %ah,(%rax)
	...
 40d:	00 00                	add    %al,(%rax)
 40f:	00 01                	add    %al,(%rcx)
 411:	00 00                	add    %al,(%rax)
 413:	00 12                	add    %dl,(%rdx)
	...
 425:	00 00                	add    %al,(%rax)
 427:	00 06                	add    %al,(%rsi)
 429:	00 00                	add    %al,(%rax)
 42b:	00 12                	add    %dl,(%rdx)
	...
 43d:	00 00                	add    %al,(%rax)
 43f:	00 35 00 00 00 12    	add    %dh,0x12000000(%rip)        # 12000445 <__cxa_finalize@plt+0x11fff3d5>
	...
 455:	00 00                	add    %al,(%rax)
 457:	00 79 00             	add    %bh,0x0(%rcx)
 45a:	00 00                	add    %al,(%rax)
 45c:	20 00                	and    %al,(%rax)
	...
 46e:	00 00                	add    %al,(%rax)
 470:	0d 00 00 00 12       	or     $0x12000000,%eax
	...
 485:	00 00                	add    %al,(%rax)
 487:	00 88 00 00 00 20    	add    %cl,0x20000000(%rax)
	...
 49d:	00 00                	add    %al,(%rax)
 49f:	00 26                	add    %ah,(%rsi)
 4a1:	00 00                	add    %al,(%rax)
 4a3:	00 22                	add    %ah,(%rdx)
	...

Disassembly of section .dynstr:

00000000000004b8 <.dynstr>:
 4b8:	00 70 75             	add    %dh,0x75(%rax)
 4bb:	74 73                	je     530 <puts@plt-0xb00>
 4bd:	00 73 74             	add    %dh,0x74(%rbx)
 4c0:	72 6c                	jb     52e <puts@plt-0xb02>
 4c2:	65 6e                	outsb  %gs:(%rsi),(%dx)
 4c4:	00 6d 61             	add    %ch,0x61(%rbp)
 4c7:	6c                   	insb   (%dx),%es:(%rdi)
 4c8:	6c                   	insb   (%dx),%es:(%rdi)
 4c9:	6f                   	outsl  %ds:(%rsi),(%dx)
 4ca:	63 00                	movsxd (%rax),%eax
 4cc:	5f                   	pop    %rdi
 4cd:	5f                   	pop    %rdi
 4ce:	6c                   	insb   (%dx),%es:(%rdi)
 4cf:	69 62 63 5f 73 74 61 	imul   $0x6174735f,0x63(%rdx),%esp
 4d6:	72 74                	jb     54c <puts@plt-0xae4>
 4d8:	5f                   	pop    %rdi
 4d9:	6d                   	insl   (%dx),%es:(%rdi)
 4da:	61                   	(bad)
 4db:	69 6e 00 5f 5f 63 78 	imul   $0x78635f5f,0x0(%rsi),%ebp
 4e2:	61                   	(bad)
 4e3:	5f                   	pop    %rdi
 4e4:	66 69 6e 61 6c 69    	imul   $0x696c,0x61(%rsi),%bp
 4ea:	7a 65                	jp     551 <puts@plt-0xadf>
 4ec:	00 70 72             	add    %dh,0x72(%rax)
 4ef:	69 6e 74 66 00 6c 69 	imul   $0x696c0066,0x74(%rsi),%ebp
 4f6:	62 63 2e 73 6f       	(bad)
 4fb:	2e 36 00 47 4c       	cs ss add %al,0x4c(%rdi)
 500:	49                   	rex.WB
 501:	42                   	rex.X
 502:	43 5f                	rex.XB pop %r15
 504:	32 2e                	xor    (%rsi),%ch
 506:	32 2e                	xor    (%rsi),%ch
 508:	35 00 47 4c 49       	xor    $0x494c4700,%eax
 50d:	42                   	rex.X
 50e:	43 5f                	rex.XB pop %r15
 510:	32 2e                	xor    (%rsi),%ch
 512:	33 34 00             	xor    (%rax,%rax,1),%esi
 515:	5f                   	pop    %rdi
 516:	49 54                	rex.WB push %r12
 518:	4d 5f                	rex.WRB pop %r15
 51a:	64 65 72 65          	fs gs jb 583 <puts@plt-0xaad>
 51e:	67 69 73 74 65 72 54 	imul   $0x4d547265,0x74(%ebx),%esi
 525:	4d 
 526:	43 6c                	rex.XB insb (%dx),%es:(%rdi)
 528:	6f                   	outsl  %ds:(%rsi),(%dx)
 529:	6e                   	outsb  %ds:(%rsi),(%dx)
 52a:	65 54                	gs push %rsp
 52c:	61                   	(bad)
 52d:	62                   	(bad)
 52e:	6c                   	insb   (%dx),%es:(%rdi)
 52f:	65 00 5f 5f          	add    %bl,%gs:0x5f(%rdi)
 533:	67 6d                	insl   (%dx),%es:(%edi)
 535:	6f                   	outsl  %ds:(%rsi),(%dx)
 536:	6e                   	outsb  %ds:(%rsi),(%dx)
 537:	5f                   	pop    %rdi
 538:	73 74                	jae    5ae <puts@plt-0xa82>
 53a:	61                   	(bad)
 53b:	72 74                	jb     5b1 <puts@plt-0xa7f>
 53d:	5f                   	pop    %rdi
 53e:	5f                   	pop    %rdi
 53f:	00 5f 49             	add    %bl,0x49(%rdi)
 542:	54                   	push   %rsp
 543:	4d 5f                	rex.WRB pop %r15
 545:	72 65                	jb     5ac <puts@plt-0xa84>
 547:	67 69 73 74 65 72 54 	imul   $0x4d547265,0x74(%ebx),%esi
 54e:	4d 
 54f:	43 6c                	rex.XB insb (%dx),%es:(%rdi)
 551:	6f                   	outsl  %ds:(%rsi),(%dx)
 552:	6e                   	outsb  %ds:(%rsi),(%dx)
 553:	65 54                	gs push %rsp
 555:	61                   	(bad)
 556:	62                   	.byte 0x62
 557:	6c                   	insb   (%dx),%es:(%rdi)
 558:	65                   	gs
	...

Disassembly of section .gnu.version:

000000000000055a <.gnu.version>:
 55a:	00 00                	add    %al,(%rax)
 55c:	02 00                	add    (%rax),%al
 55e:	01 00                	add    %eax,(%rax)
 560:	03 00                	add    (%rax),%eax
 562:	03 00                	add    (%rax),%eax
 564:	03 00                	add    (%rax),%eax
 566:	01 00                	add    %eax,(%rax)
 568:	03 00                	add    (%rax),%eax
 56a:	01 00                	add    %eax,(%rax)
 56c:	03 00                	add    (%rax),%eax

Disassembly of section .gnu.version_r:

0000000000000570 <.gnu.version_r>:
 570:	01 00                	add    %eax,(%rax)
 572:	02 00                	add    (%rax),%al
 574:	3c 00                	cmp    $0x0,%al
 576:	00 00                	add    %al,(%rax)
 578:	10 00                	adc    %al,(%rax)
 57a:	00 00                	add    %al,(%rax)
 57c:	00 00                	add    %al,(%rax)
 57e:	00 00                	add    %al,(%rax)
 580:	75 1a                	jne    59c <puts@plt-0xa94>
 582:	69 09 00 00 03 00    	imul   $0x30000,(%rcx),%ecx
 588:	46 00 00             	rex.RX add %r8b,(%rax)
 58b:	00 10                	add    %dl,(%rax)
 58d:	00 00                	add    %al,(%rax)
 58f:	00 b4 91 96 06 00 00 	add    %dh,0x696(%rcx,%rdx,4)
 596:	02 00                	add    (%rax),%al
 598:	52                   	push   %rdx
 599:	00 00                	add    %al,(%rax)
 59b:	00 00                	add    %al,(%rax)
 59d:	00 00                	add    %al,(%rax)
	...

Disassembly of section .rela.dyn:

00000000000005a0 <.rela.dyn>:
 5a0:	d0 3d 00 00 00 00    	sarb   0x0(%rip)        # 5a6 <puts@plt-0xa8a>
 5a6:	00 00                	add    %al,(%rax)
 5a8:	08 00                	or     %al,(%rax)
 5aa:	00 00                	add    %al,(%rax)
 5ac:	00 00                	add    %al,(%rax)
 5ae:	00 00                	add    %al,(%rax)
 5b0:	60                   	(bad)
 5b1:	11 00                	adc    %eax,(%rax)
 5b3:	00 00                	add    %al,(%rax)
 5b5:	00 00                	add    %al,(%rax)
 5b7:	00 d8                	add    %bl,%al
 5b9:	3d 00 00 00 00       	cmp    $0x0,%eax
 5be:	00 00                	add    %al,(%rax)
 5c0:	08 00                	or     %al,(%rax)
 5c2:	00 00                	add    %al,(%rax)
 5c4:	00 00                	add    %al,(%rax)
 5c6:	00 00                	add    %al,(%rax)
 5c8:	20 11                	and    %dl,(%rcx)
 5ca:	00 00                	add    %al,(%rax)
 5cc:	00 00                	add    %al,(%rax)
 5ce:	00 00                	add    %al,(%rax)
 5d0:	28 40 00             	sub    %al,0x0(%rax)
 5d3:	00 00                	add    %al,(%rax)
 5d5:	00 00                	add    %al,(%rax)
 5d7:	00 08                	add    %cl,(%rax)
 5d9:	00 00                	add    %al,(%rax)
 5db:	00 00                	add    %al,(%rax)
 5dd:	00 00                	add    %al,(%rax)
 5df:	00 28                	add    %ch,(%rax)
 5e1:	40 00 00             	rex add %al,(%rax)
 5e4:	00 00                	add    %al,(%rax)
 5e6:	00 00                	add    %al,(%rax)
 5e8:	c0 3f 00             	sarb   $0x0,(%rdi)
 5eb:	00 00                	add    %al,(%rax)
 5ed:	00 00                	add    %al,(%rax)
 5ef:	00 06                	add    %al,(%rsi)
 5f1:	00 00                	add    %al,(%rax)
 5f3:	00 01                	add    %al,(%rcx)
	...
 5fd:	00 00                	add    %al,(%rax)
 5ff:	00 c8                	add    %cl,%al
 601:	3f                   	(bad)
 602:	00 00                	add    %al,(%rax)
 604:	00 00                	add    %al,(%rax)
 606:	00 00                	add    %al,(%rax)
 608:	06                   	(bad)
 609:	00 00                	add    %al,(%rax)
 60b:	00 02                	add    %al,(%rdx)
	...
 615:	00 00                	add    %al,(%rax)
 617:	00 d0                	add    %dl,%al
 619:	3f                   	(bad)
 61a:	00 00                	add    %al,(%rax)
 61c:	00 00                	add    %al,(%rax)
 61e:	00 00                	add    %al,(%rax)
 620:	06                   	(bad)
 621:	00 00                	add    %al,(%rax)
 623:	00 06                	add    %al,(%rsi)
	...
 62d:	00 00                	add    %al,(%rax)
 62f:	00 d8                	add    %bl,%al
 631:	3f                   	(bad)
 632:	00 00                	add    %al,(%rax)
 634:	00 00                	add    %al,(%rax)
 636:	00 00                	add    %al,(%rax)
 638:	06                   	(bad)
 639:	00 00                	add    %al,(%rax)
 63b:	00 08                	add    %cl,(%rax)
	...
 645:	00 00                	add    %al,(%rax)
 647:	00 e0                	add    %ah,%al
 649:	3f                   	(bad)
 64a:	00 00                	add    %al,(%rax)
 64c:	00 00                	add    %al,(%rax)
 64e:	00 00                	add    %al,(%rax)
 650:	06                   	(bad)
 651:	00 00                	add    %al,(%rax)
 653:	00 09                	add    %cl,(%rcx)
	...

Disassembly of section .rela.plt:

0000000000000660 <.rela.plt>:
 660:	00 40 00             	add    %al,0x0(%rax)
 663:	00 00                	add    %al,(%rax)
 665:	00 00                	add    %al,(%rax)
 667:	00 07                	add    %al,(%rdi)
 669:	00 00                	add    %al,(%rax)
 66b:	00 03                	add    %al,(%rbx)
	...
 675:	00 00                	add    %al,(%rax)
 677:	00 08                	add    %cl,(%rax)
 679:	40 00 00             	rex add %al,(%rax)
 67c:	00 00                	add    %al,(%rax)
 67e:	00 00                	add    %al,(%rax)
 680:	07                   	(bad)
 681:	00 00                	add    %al,(%rax)
 683:	00 04 00             	add    %al,(%rax,%rax,1)
	...
 68e:	00 00                	add    %al,(%rax)
 690:	10 40 00             	adc    %al,0x0(%rax)
 693:	00 00                	add    %al,(%rax)
 695:	00 00                	add    %al,(%rax)
 697:	00 07                	add    %al,(%rdi)
 699:	00 00                	add    %al,(%rax)
 69b:	00 05 00 00 00 00    	add    %al,0x0(%rip)        # 6a1 <puts@plt-0x98f>
 6a1:	00 00                	add    %al,(%rax)
 6a3:	00 00                	add    %al,(%rax)
 6a5:	00 00                	add    %al,(%rax)
 6a7:	00 18                	add    %bl,(%rax)
 6a9:	40 00 00             	rex add %al,(%rax)
 6ac:	00 00                	add    %al,(%rax)
 6ae:	00 00                	add    %al,(%rax)
 6b0:	07                   	(bad)
 6b1:	00 00                	add    %al,(%rax)
 6b3:	00 07                	add    %al,(%rdi)
	...

Disassembly of section .init:

0000000000001000 <.init>:
    1000:	48 83 ec 08          	sub    $0x8,%rsp
    1004:	48 8b 05 c5 2f 00 00 	mov    0x2fc5(%rip),%rax        # 3fd0 <__cxa_finalize@plt+0x2f60>
    100b:	48 85 c0             	test   %rax,%rax
    100e:	74 02                	je     1012 <puts@plt-0x1e>
    1010:	ff d0                	call   *%rax
    1012:	48 83 c4 08          	add    $0x8,%rsp
    1016:	c3                   	ret

Disassembly of section .plt:

0000000000001020 <puts@plt-0x10>:
    1020:	ff 35 ca 2f 00 00    	push   0x2fca(%rip)        # 3ff0 <__cxa_finalize@plt+0x2f80>
    1026:	ff 25 cc 2f 00 00    	jmp    *0x2fcc(%rip)        # 3ff8 <__cxa_finalize@plt+0x2f88>
    102c:	0f 1f 40 00          	nopl   0x0(%rax)

0000000000001030 <puts@plt>:
    1030:	ff 25 ca 2f 00 00    	jmp    *0x2fca(%rip)        # 4000 <__cxa_finalize@plt+0x2f90>
    1036:	68 00 00 00 00       	push   $0x0
    103b:	e9 e0 ff ff ff       	jmp    1020 <puts@plt-0x10>

0000000000001040 <strlen@plt>:
    1040:	ff 25 c2 2f 00 00    	jmp    *0x2fc2(%rip)        # 4008 <__cxa_finalize@plt+0x2f98>
    1046:	68 01 00 00 00       	push   $0x1
    104b:	e9 d0 ff ff ff       	jmp    1020 <puts@plt-0x10>

0000000000001050 <printf@plt>:
    1050:	ff 25 ba 2f 00 00    	jmp    *0x2fba(%rip)        # 4010 <__cxa_finalize@plt+0x2fa0>
    1056:	68 02 00 00 00       	push   $0x2
    105b:	e9 c0 ff ff ff       	jmp    1020 <puts@plt-0x10>

0000000000001060 <malloc@plt>:
    1060:	ff 25 b2 2f 00 00    	jmp    *0x2fb2(%rip)        # 4018 <__cxa_finalize@plt+0x2fa8>
    1066:	68 03 00 00 00       	push   $0x3
    106b:	e9 b0 ff ff ff       	jmp    1020 <puts@plt-0x10>

Disassembly of section .plt.got:

0000000000001070 <__cxa_finalize@plt>:
    1070:	ff 25 6a 2f 00 00    	jmp    *0x2f6a(%rip)        # 3fe0 <__cxa_finalize@plt+0x2f70>
    1076:	66 90                	xchg   %ax,%ax

Disassembly of section .text:

0000000000001080 <.text>:
    1080:	31 ed                	xor    %ebp,%ebp
    1082:	49 89 d1             	mov    %rdx,%r9
    1085:	5e                   	pop    %rsi
    1086:	48 89 e2             	mov    %rsp,%rdx
    1089:	48 83 e4 f0          	and    $0xfffffffffffffff0,%rsp
    108d:	50                   	push   %rax
    108e:	54                   	push   %rsp
    108f:	45 31 c0             	xor    %r8d,%r8d
    1092:	31 c9                	xor    %ecx,%ecx
    1094:	48 8d 3d 55 01 00 00 	lea    0x155(%rip),%rdi        # 11f0 <__cxa_finalize@plt+0x180>
    109b:	ff 15 1f 2f 00 00    	call   *0x2f1f(%rip)        # 3fc0 <__cxa_finalize@plt+0x2f50>
    10a1:	f4                   	hlt
    10a2:	66 2e 0f 1f 84 00 00 	cs nopw 0x0(%rax,%rax,1)
    10a9:	00 00 00 
    10ac:	0f 1f 40 00          	nopl   0x0(%rax)
    10b0:	48 8d 3d 79 2f 00 00 	lea    0x2f79(%rip),%rdi        # 4030 <__cxa_finalize@plt+0x2fc0>
    10b7:	48 8d 05 72 2f 00 00 	lea    0x2f72(%rip),%rax        # 4030 <__cxa_finalize@plt+0x2fc0>
    10be:	48 39 f8             	cmp    %rdi,%rax
    10c1:	74 15                	je     10d8 <__cxa_finalize@plt+0x68>
    10c3:	48 8b 05 fe 2e 00 00 	mov    0x2efe(%rip),%rax        # 3fc8 <__cxa_finalize@plt+0x2f58>
    10ca:	48 85 c0             	test   %rax,%rax
    10cd:	74 09                	je     10d8 <__cxa_finalize@plt+0x68>
    10cf:	ff e0                	jmp    *%rax
    10d1:	0f 1f 80 00 00 00 00 	nopl   0x0(%rax)
    10d8:	c3                   	ret
    10d9:	0f 1f 80 00 00 00 00 	nopl   0x0(%rax)
    10e0:	48 8d 3d 49 2f 00 00 	lea    0x2f49(%rip),%rdi        # 4030 <__cxa_finalize@plt+0x2fc0>
    10e7:	48 8d 35 42 2f 00 00 	lea    0x2f42(%rip),%rsi        # 4030 <__cxa_finalize@plt+0x2fc0>
    10ee:	48 29 fe             	sub    %rdi,%rsi
    10f1:	48 89 f0             	mov    %rsi,%rax
    10f4:	48 c1 ee 3f          	shr    $0x3f,%rsi
    10f8:	48 c1 f8 03          	sar    $0x3,%rax
    10fc:	48 01 c6             	add    %rax,%rsi
    10ff:	48 d1 fe             	sar    %rsi
    1102:	74 14                	je     1118 <__cxa_finalize@plt+0xa8>
    1104:	48 8b 05 cd 2e 00 00 	mov    0x2ecd(%rip),%rax        # 3fd8 <__cxa_finalize@plt+0x2f68>
    110b:	48 85 c0             	test   %rax,%rax
    110e:	74 08                	je     1118 <__cxa_finalize@plt+0xa8>
    1110:	ff e0                	jmp    *%rax
    1112:	66 0f 1f 44 00 00    	nopw   0x0(%rax,%rax,1)
    1118:	c3                   	ret
    1119:	0f 1f 80 00 00 00 00 	nopl   0x0(%rax)
    1120:	f3 0f 1e fa          	endbr64
    1124:	80 3d 05 2f 00 00 00 	cmpb   $0x0,0x2f05(%rip)        # 4030 <__cxa_finalize@plt+0x2fc0>
    112b:	75 2b                	jne    1158 <__cxa_finalize@plt+0xe8>
    112d:	55                   	push   %rbp
    112e:	48 83 3d aa 2e 00 00 	cmpq   $0x0,0x2eaa(%rip)        # 3fe0 <__cxa_finalize@plt+0x2f70>
    1135:	00 
    1136:	48 89 e5             	mov    %rsp,%rbp
    1139:	74 0c                	je     1147 <__cxa_finalize@plt+0xd7>
    113b:	48 8b 3d e6 2e 00 00 	mov    0x2ee6(%rip),%rdi        # 4028 <__cxa_finalize@plt+0x2fb8>
    1142:	e8 29 ff ff ff       	call   1070 <__cxa_finalize@plt>
    1147:	e8 64 ff ff ff       	call   10b0 <__cxa_finalize@plt+0x40>
    114c:	c6 05 dd 2e 00 00 01 	movb   $0x1,0x2edd(%rip)        # 4030 <__cxa_finalize@plt+0x2fc0>
    1153:	5d                   	pop    %rbp
    1154:	c3                   	ret
    1155:	0f 1f 00             	nopl   (%rax)
    1158:	c3                   	ret
    1159:	0f 1f 80 00 00 00 00 	nopl   0x0(%rax)
    1160:	f3 0f 1e fa          	endbr64
    1164:	e9 77 ff ff ff       	jmp    10e0 <__cxa_finalize@plt+0x70>
    1169:	55                   	push   %rbp
    116a:	48 89 e5             	mov    %rsp,%rbp
    116d:	48 83 ec 20          	sub    $0x20,%rsp
    1171:	48 89 7d e8          	mov    %rdi,-0x18(%rbp)
    1175:	89 75 e4             	mov    %esi,-0x1c(%rbp)
    1178:	8b 45 e4             	mov    -0x1c(%rbp),%eax
    117b:	83 c0 01             	add    $0x1,%eax
    117e:	48 98                	cltq
    1180:	48 89 c7             	mov    %rax,%rdi
    1183:	e8 d8 fe ff ff       	call   1060 <malloc@plt>
    1188:	48 89 45 f0          	mov    %rax,-0x10(%rbp)
    118c:	c7 45 fc 00 00 00 00 	movl   $0x0,-0x4(%rbp)
    1193:	eb 3d                	jmp    11d2 <__cxa_finalize@plt+0x162>
    1195:	8b 45 fc             	mov    -0x4(%rbp),%eax
    1198:	48 98                	cltq
    119a:	48 8d 50 01          	lea    0x1(%rax),%rdx
    119e:	48 8b 45 e8          	mov    -0x18(%rbp),%rax
    11a2:	48 01 d0             	add    %rdx,%rax
    11a5:	0f b6 08             	movzbl (%rax),%ecx
    11a8:	8b 45 fc             	mov    -0x4(%rbp),%eax
    11ab:	48 63 d0             	movslq %eax,%rdx
    11ae:	48 8b 45 e8          	mov    -0x18(%rbp),%rax
    11b2:	48 01 d0             	add    %rdx,%rax
    11b5:	0f b6 00             	movzbl (%rax),%eax
    11b8:	31 c8                	xor    %ecx,%eax
    11ba:	8d 48 21             	lea    0x21(%rax),%ecx
    11bd:	8b 45 fc             	mov    -0x4(%rbp),%eax
    11c0:	48 63 d0             	movslq %eax,%rdx
    11c3:	48 8b 45 f0          	mov    -0x10(%rbp),%rax
    11c7:	48 01 d0             	add    %rdx,%rax
    11ca:	89 ca                	mov    %ecx,%edx
    11cc:	88 10                	mov    %dl,(%rax)
    11ce:	83 45 fc 01          	addl   $0x1,-0x4(%rbp)
    11d2:	8b 45 fc             	mov    -0x4(%rbp),%eax
    11d5:	3b 45 e4             	cmp    -0x1c(%rbp),%eax
    11d8:	7c bb                	jl     1195 <__cxa_finalize@plt+0x125>
    11da:	8b 45 fc             	mov    -0x4(%rbp),%eax
    11dd:	48 63 d0             	movslq %eax,%rdx
    11e0:	48 8b 45 f0          	mov    -0x10(%rbp),%rax
    11e4:	48 01 d0             	add    %rdx,%rax
    11e7:	c6 00 00             	movb   $0x0,(%rax)
    11ea:	48 8b 45 f0          	mov    -0x10(%rbp),%rax
    11ee:	c9                   	leave
    11ef:	c3                   	ret
    11f0:	55                   	push   %rbp
    11f1:	48 89 e5             	mov    %rsp,%rbp
    11f4:	48 83 ec 20          	sub    $0x20,%rsp
    11f8:	89 7d ec             	mov    %edi,-0x14(%rbp)
    11fb:	48 89 75 e0          	mov    %rsi,-0x20(%rbp)
    11ff:	83 7d ec 02          	cmpl   $0x2,-0x14(%rbp)
    1203:	74 11                	je     1216 <__cxa_finalize@plt+0x1a6>
    1205:	48 8d 05 f8 0d 00 00 	lea    0xdf8(%rip),%rax        # 2004 <__cxa_finalize@plt+0xf94>
    120c:	48 89 c7             	mov    %rax,%rdi
    120f:	e8 1c fe ff ff       	call   1030 <puts@plt>
    1214:	eb 78                	jmp    128e <__cxa_finalize@plt+0x21e>
    1216:	48 8b 45 e0          	mov    -0x20(%rbp),%rax
    121a:	48 83 c0 08          	add    $0x8,%rax
    121e:	48 8b 00             	mov    (%rax),%rax
    1221:	48 89 c7             	mov    %rax,%rdi
    1224:	e8 17 fe ff ff       	call   1040 <strlen@plt>
    1229:	89 c2                	mov    %eax,%edx
    122b:	48 8b 45 e0          	mov    -0x20(%rbp),%rax
    122f:	48 83 c0 08          	add    $0x8,%rax
    1233:	48 8b 00             	mov    (%rax),%rax
    1236:	89 d6                	mov    %edx,%esi
    1238:	48 89 c7             	mov    %rax,%rdi
    123b:	e8 29 ff ff ff       	call   1169 <__cxa_finalize@plt+0xf9>
    1240:	48 89 45 f0          	mov    %rax,-0x10(%rbp)
    1244:	c7 45 fc 00 00 00 00 	movl   $0x0,-0x4(%rbp)
    124b:	eb 2d                	jmp    127a <__cxa_finalize@plt+0x20a>
    124d:	8b 45 fc             	mov    -0x4(%rbp),%eax
    1250:	48 63 d0             	movslq %eax,%rdx
    1253:	48 8b 45 f0          	mov    -0x10(%rbp),%rax
    1257:	48 01 d0             	add    %rdx,%rax
    125a:	0f b6 00             	movzbl (%rax),%eax
    125d:	0f b6 c0             	movzbl %al,%eax
    1260:	89 c6                	mov    %eax,%esi
    1262:	48 8d 05 b3 0d 00 00 	lea    0xdb3(%rip),%rax        # 201c <__cxa_finalize@plt+0xfac>
    1269:	48 89 c7             	mov    %rax,%rdi
    126c:	b8 00 00 00 00       	mov    $0x0,%eax
    1271:	e8 da fd ff ff       	call   1050 <printf@plt>
    1276:	83 45 fc 01          	addl   $0x1,-0x4(%rbp)
    127a:	8b 45 fc             	mov    -0x4(%rbp),%eax
    127d:	48 63 d0             	movslq %eax,%rdx
    1280:	48 8b 45 f0          	mov    -0x10(%rbp),%rax
    1284:	48 01 d0             	add    %rdx,%rax
    1287:	0f b6 00             	movzbl (%rax),%eax
    128a:	84 c0                	test   %al,%al
    128c:	75 bf                	jne    124d <__cxa_finalize@plt+0x1dd>
    128e:	b8 00 00 00 00       	mov    $0x0,%eax
    1293:	c9                   	leave
    1294:	c3                   	ret

Disassembly of section .fini:

0000000000001298 <.fini>:
    1298:	48 83 ec 08          	sub    $0x8,%rsp
    129c:	48 83 c4 08          	add    $0x8,%rsp
    12a0:	c3                   	ret

Disassembly of section .rodata:

0000000000002000 <.rodata>:
    2000:	01 00                	add    %eax,(%rax)
    2002:	02 00                	add    (%rax),%al
    2004:	75 73                	jne    2079 <__cxa_finalize@plt+0x1009>
    2006:	61                   	(bad)
    2007:	67 65 3a 20          	cmp    %gs:(%eax),%ah
    200b:	2e 2f                	cs (bad)
    200d:	65 6e                	outsb  %gs:(%rsi),(%dx)
    200f:	63 72 79             	movsxd 0x79(%rdx),%esi
    2012:	70 74                	jo     2088 <__cxa_finalize@plt+0x1018>
    2014:	20 3c 66             	and    %bh,(%rsi,%riz,2)
    2017:	6c                   	insb   (%dx),%es:(%rdi)
    2018:	61                   	(bad)
    2019:	67 3e 00 25 30 32 78 	ds add %ah,0x783230(%eip)        # 785251 <__cxa_finalize@plt+0x7841e1>
    2020:	00 

Disassembly of section .eh_frame_hdr:

0000000000002024 <.eh_frame_hdr>:
    2024:	01 1b                	add    %ebx,(%rbx)
    2026:	03 3b                	add    (%rbx),%edi
    2028:	30 00                	xor    %al,(%rax)
    202a:	00 00                	add    %al,(%rax)
    202c:	05 00 00 00 fc       	add    $0xfc000000,%eax
    2031:	ef                   	out    %eax,(%dx)
    2032:	ff                   	(bad)
    2033:	ff                   	(bad)
    2034:	7c 00                	jl     2036 <__cxa_finalize@plt+0xfc6>
    2036:	00 00                	add    %al,(%rax)
    2038:	4c                   	rex.WR
    2039:	f0 ff                	lock (bad)
    203b:	ff a4 00 00 00 5c f0 	jmp    *-0xfa40000(%rax,%rax,1)
    2042:	ff                   	(bad)
    2043:	ff 4c 00 00          	decl   0x0(%rax,%rax,1)
    2047:	00 45 f1             	add    %al,-0xf(%rbp)
    204a:	ff                   	(bad)
    204b:	ff                   	(bad)
    204c:	bc 00 00 00 cc       	mov    $0xcc000000,%esp
    2051:	f1                   	int1
    2052:	ff                   	(bad)
    2053:	ff                   	(bad)
    2054:	dc 00                	faddl  (%rax)
	...

Disassembly of section .eh_frame:

0000000000002058 <.eh_frame>:
    2058:	14 00                	adc    $0x0,%al
    205a:	00 00                	add    %al,(%rax)
    205c:	00 00                	add    %al,(%rax)
    205e:	00 00                	add    %al,(%rax)
    2060:	01 7a 52             	add    %edi,0x52(%rdx)
    2063:	00 01                	add    %al,(%rcx)
    2065:	78 10                	js     2077 <__cxa_finalize@plt+0x1007>
    2067:	01 1b                	add    %ebx,(%rbx)
    2069:	0c 07                	or     $0x7,%al
    206b:	08 90 01 07 10 14    	or     %dl,0x14100701(%rax)
    2071:	00 00                	add    %al,(%rax)
    2073:	00 1c 00             	add    %bl,(%rax,%rax,1)
    2076:	00 00                	add    %al,(%rax)
    2078:	08 f0                	or     %dh,%al
    207a:	ff                   	(bad)
    207b:	ff 22                	jmp    *(%rdx)
	...
    2085:	00 00                	add    %al,(%rax)
    2087:	00 14 00             	add    %dl,(%rax,%rax,1)
    208a:	00 00                	add    %al,(%rax)
    208c:	00 00                	add    %al,(%rax)
    208e:	00 00                	add    %al,(%rax)
    2090:	01 7a 52             	add    %edi,0x52(%rdx)
    2093:	00 01                	add    %al,(%rcx)
    2095:	78 10                	js     20a7 <__cxa_finalize@plt+0x1037>
    2097:	01 1b                	add    %ebx,(%rbx)
    2099:	0c 07                	or     $0x7,%al
    209b:	08 90 01 00 00 24    	or     %dl,0x24000001(%rax)
    20a1:	00 00                	add    %al,(%rax)
    20a3:	00 1c 00             	add    %bl,(%rax,%rax,1)
    20a6:	00 00                	add    %al,(%rax)
    20a8:	78 ef                	js     2099 <__cxa_finalize@plt+0x1029>
    20aa:	ff                   	(bad)
    20ab:	ff 50 00             	call   *0x0(%rax)
    20ae:	00 00                	add    %al,(%rax)
    20b0:	00 0e                	add    %cl,(%rsi)
    20b2:	10 46 0e             	adc    %al,0xe(%rsi)
    20b5:	18 4a 0f             	sbb    %cl,0xf(%rdx)
    20b8:	0b 77 08             	or     0x8(%rdi),%esi
    20bb:	80 00 3f             	addb   $0x3f,(%rax)
    20be:	1a 3b                	sbb    (%rbx),%bh
    20c0:	2a 33                	sub    (%rbx),%dh
    20c2:	24 22                	and    $0x22,%al
    20c4:	00 00                	add    %al,(%rax)
    20c6:	00 00                	add    %al,(%rax)
    20c8:	14 00                	adc    $0x0,%al
    20ca:	00 00                	add    %al,(%rax)
    20cc:	44 00 00             	add    %r8b,(%rax)
    20cf:	00 a0 ef ff ff 08    	add    %ah,0x8ffffef(%rax)
	...
    20dd:	00 00                	add    %al,(%rax)
    20df:	00 1c 00             	add    %bl,(%rax,%rax,1)
    20e2:	00 00                	add    %al,(%rax)
    20e4:	5c                   	pop    %rsp
    20e5:	00 00                	add    %al,(%rax)
    20e7:	00 81 f0 ff ff 87    	add    %al,-0x78000010(%rcx)
    20ed:	00 00                	add    %al,(%rax)
    20ef:	00 00                	add    %al,(%rax)
    20f1:	41 0e                	rex.B (bad)
    20f3:	10 86 02 43 0d 06    	adc    %al,0x60d4302(%rsi)
    20f9:	02 82 0c 07 08 00    	add    0x8070c(%rdx),%al
    20ff:	00 1c 00             	add    %bl,(%rax,%rax,1)
    2102:	00 00                	add    %al,(%rax)
    2104:	7c 00                	jl     2106 <__cxa_finalize@plt+0x1096>
    2106:	00 00                	add    %al,(%rax)
    2108:	e8 f0 ff ff a5       	call   ffffffffa60020fd <__cxa_finalize@plt+0xffffffffa600108d>
    210d:	00 00                	add    %al,(%rax)
    210f:	00 00                	add    %al,(%rax)
    2111:	41 0e                	rex.B (bad)
    2113:	10 86 02 43 0d 06    	adc    %al,0x60d4302(%rsi)
    2119:	02 a0 0c 07 08 00    	add    0x8070c(%rax),%ah
    211f:	00 00                	add    %al,(%rax)
    2121:	00 00                	add    %al,(%rax)
	...

Disassembly of section .init_array:

0000000000003dd0 <.init_array>:
    3dd0:	60                   	(bad)
    3dd1:	11 00                	adc    %eax,(%rax)
    3dd3:	00 00                	add    %al,(%rax)
    3dd5:	00 00                	add    %al,(%rax)
	...

Disassembly of section .fini_array:

0000000000003dd8 <.fini_array>:
    3dd8:	20 11                	and    %dl,(%rcx)
    3dda:	00 00                	add    %al,(%rax)
    3ddc:	00 00                	add    %al,(%rax)
	...

Disassembly of section .dynamic:

0000000000003de0 <.dynamic>:
    3de0:	01 00                	add    %eax,(%rax)
    3de2:	00 00                	add    %al,(%rax)
    3de4:	00 00                	add    %al,(%rax)
    3de6:	00 00                	add    %al,(%rax)
    3de8:	3c 00                	cmp    $0x0,%al
    3dea:	00 00                	add    %al,(%rax)
    3dec:	00 00                	add    %al,(%rax)
    3dee:	00 00                	add    %al,(%rax)
    3df0:	0c 00                	or     $0x0,%al
    3df2:	00 00                	add    %al,(%rax)
    3df4:	00 00                	add    %al,(%rax)
    3df6:	00 00                	add    %al,(%rax)
    3df8:	00 10                	add    %dl,(%rax)
    3dfa:	00 00                	add    %al,(%rax)
    3dfc:	00 00                	add    %al,(%rax)
    3dfe:	00 00                	add    %al,(%rax)
    3e00:	0d 00 00 00 00       	or     $0x0,%eax
    3e05:	00 00                	add    %al,(%rax)
    3e07:	00 98 12 00 00 00    	add    %bl,0x12(%rax)
    3e0d:	00 00                	add    %al,(%rax)
    3e0f:	00 19                	add    %bl,(%rcx)
    3e11:	00 00                	add    %al,(%rax)
    3e13:	00 00                	add    %al,(%rax)
    3e15:	00 00                	add    %al,(%rax)
    3e17:	00 d0                	add    %dl,%al
    3e19:	3d 00 00 00 00       	cmp    $0x0,%eax
    3e1e:	00 00                	add    %al,(%rax)
    3e20:	1b 00                	sbb    (%rax),%eax
    3e22:	00 00                	add    %al,(%rax)
    3e24:	00 00                	add    %al,(%rax)
    3e26:	00 00                	add    %al,(%rax)
    3e28:	08 00                	or     %al,(%rax)
    3e2a:	00 00                	add    %al,(%rax)
    3e2c:	00 00                	add    %al,(%rax)
    3e2e:	00 00                	add    %al,(%rax)
    3e30:	1a 00                	sbb    (%rax),%al
    3e32:	00 00                	add    %al,(%rax)
    3e34:	00 00                	add    %al,(%rax)
    3e36:	00 00                	add    %al,(%rax)
    3e38:	d8 3d 00 00 00 00    	fdivrs 0x0(%rip)        # 3e3e <__cxa_finalize@plt+0x2dce>
    3e3e:	00 00                	add    %al,(%rax)
    3e40:	1c 00                	sbb    $0x0,%al
    3e42:	00 00                	add    %al,(%rax)
    3e44:	00 00                	add    %al,(%rax)
    3e46:	00 00                	add    %al,(%rax)
    3e48:	08 00                	or     %al,(%rax)
    3e4a:	00 00                	add    %al,(%rax)
    3e4c:	00 00                	add    %al,(%rax)
    3e4e:	00 00                	add    %al,(%rax)
    3e50:	f5                   	cmc
    3e51:	fe                   	(bad)
    3e52:	ff 6f 00             	ljmp   *0x0(%rdi)
    3e55:	00 00                	add    %al,(%rax)
    3e57:	00 a0 03 00 00 00    	add    %ah,0x3(%rax)
    3e5d:	00 00                	add    %al,(%rax)
    3e5f:	00 05 00 00 00 00    	add    %al,0x0(%rip)        # 3e65 <__cxa_finalize@plt+0x2df5>
    3e65:	00 00                	add    %al,(%rax)
    3e67:	00 b8 04 00 00 00    	add    %bh,0x4(%rax)
    3e6d:	00 00                	add    %al,(%rax)
    3e6f:	00 06                	add    %al,(%rsi)
    3e71:	00 00                	add    %al,(%rax)
    3e73:	00 00                	add    %al,(%rax)
    3e75:	00 00                	add    %al,(%rax)
    3e77:	00 c8                	add    %cl,%al
    3e79:	03 00                	add    (%rax),%eax
    3e7b:	00 00                	add    %al,(%rax)
    3e7d:	00 00                	add    %al,(%rax)
    3e7f:	00 0a                	add    %cl,(%rdx)
    3e81:	00 00                	add    %al,(%rax)
    3e83:	00 00                	add    %al,(%rax)
    3e85:	00 00                	add    %al,(%rax)
    3e87:	00 a2 00 00 00 00    	add    %ah,0x0(%rdx)
    3e8d:	00 00                	add    %al,(%rax)
    3e8f:	00 0b                	add    %cl,(%rbx)
    3e91:	00 00                	add    %al,(%rax)
    3e93:	00 00                	add    %al,(%rax)
    3e95:	00 00                	add    %al,(%rax)
    3e97:	00 18                	add    %bl,(%rax)
    3e99:	00 00                	add    %al,(%rax)
    3e9b:	00 00                	add    %al,(%rax)
    3e9d:	00 00                	add    %al,(%rax)
    3e9f:	00 15 00 00 00 00    	add    %dl,0x0(%rip)        # 3ea5 <__cxa_finalize@plt+0x2e35>
	...
    3ead:	00 00                	add    %al,(%rax)
    3eaf:	00 03                	add    %al,(%rbx)
    3eb1:	00 00                	add    %al,(%rax)
    3eb3:	00 00                	add    %al,(%rax)
    3eb5:	00 00                	add    %al,(%rax)
    3eb7:	00 e8                	add    %ch,%al
    3eb9:	3f                   	(bad)
    3eba:	00 00                	add    %al,(%rax)
    3ebc:	00 00                	add    %al,(%rax)
    3ebe:	00 00                	add    %al,(%rax)
    3ec0:	02 00                	add    (%rax),%al
    3ec2:	00 00                	add    %al,(%rax)
    3ec4:	00 00                	add    %al,(%rax)
    3ec6:	00 00                	add    %al,(%rax)
    3ec8:	60                   	(bad)
    3ec9:	00 00                	add    %al,(%rax)
    3ecb:	00 00                	add    %al,(%rax)
    3ecd:	00 00                	add    %al,(%rax)
    3ecf:	00 14 00             	add    %dl,(%rax,%rax,1)
    3ed2:	00 00                	add    %al,(%rax)
    3ed4:	00 00                	add    %al,(%rax)
    3ed6:	00 00                	add    %al,(%rax)
    3ed8:	07                   	(bad)
    3ed9:	00 00                	add    %al,(%rax)
    3edb:	00 00                	add    %al,(%rax)
    3edd:	00 00                	add    %al,(%rax)
    3edf:	00 17                	add    %dl,(%rdi)
    3ee1:	00 00                	add    %al,(%rax)
    3ee3:	00 00                	add    %al,(%rax)
    3ee5:	00 00                	add    %al,(%rax)
    3ee7:	00 60 06             	add    %ah,0x6(%rax)
    3eea:	00 00                	add    %al,(%rax)
    3eec:	00 00                	add    %al,(%rax)
    3eee:	00 00                	add    %al,(%rax)
    3ef0:	07                   	(bad)
    3ef1:	00 00                	add    %al,(%rax)
    3ef3:	00 00                	add    %al,(%rax)
    3ef5:	00 00                	add    %al,(%rax)
    3ef7:	00 a0 05 00 00 00    	add    %ah,0x5(%rax)
    3efd:	00 00                	add    %al,(%rax)
    3eff:	00 08                	add    %cl,(%rax)
    3f01:	00 00                	add    %al,(%rax)
    3f03:	00 00                	add    %al,(%rax)
    3f05:	00 00                	add    %al,(%rax)
    3f07:	00 c0                	add    %al,%al
    3f09:	00 00                	add    %al,(%rax)
    3f0b:	00 00                	add    %al,(%rax)
    3f0d:	00 00                	add    %al,(%rax)
    3f0f:	00 09                	add    %cl,(%rcx)
    3f11:	00 00                	add    %al,(%rax)
    3f13:	00 00                	add    %al,(%rax)
    3f15:	00 00                	add    %al,(%rax)
    3f17:	00 18                	add    %bl,(%rax)
    3f19:	00 00                	add    %al,(%rax)
    3f1b:	00 00                	add    %al,(%rax)
    3f1d:	00 00                	add    %al,(%rax)
    3f1f:	00 fb                	add    %bh,%bl
    3f21:	ff                   	(bad)
    3f22:	ff 6f 00             	ljmp   *0x0(%rdi)
    3f25:	00 00                	add    %al,(%rax)
    3f27:	00 00                	add    %al,(%rax)
    3f29:	00 00                	add    %al,(%rax)
    3f2b:	08 00                	or     %al,(%rax)
    3f2d:	00 00                	add    %al,(%rax)
    3f2f:	00 fe                	add    %bh,%dh
    3f31:	ff                   	(bad)
    3f32:	ff 6f 00             	ljmp   *0x0(%rdi)
    3f35:	00 00                	add    %al,(%rax)
    3f37:	00 70 05             	add    %dh,0x5(%rax)
    3f3a:	00 00                	add    %al,(%rax)
    3f3c:	00 00                	add    %al,(%rax)
    3f3e:	00 00                	add    %al,(%rax)
    3f40:	ff                   	(bad)
    3f41:	ff                   	(bad)
    3f42:	ff 6f 00             	ljmp   *0x0(%rdi)
    3f45:	00 00                	add    %al,(%rax)
    3f47:	00 01                	add    %al,(%rcx)
    3f49:	00 00                	add    %al,(%rax)
    3f4b:	00 00                	add    %al,(%rax)
    3f4d:	00 00                	add    %al,(%rax)
    3f4f:	00 f0                	add    %dh,%al
    3f51:	ff                   	(bad)
    3f52:	ff 6f 00             	ljmp   *0x0(%rdi)
    3f55:	00 00                	add    %al,(%rax)
    3f57:	00 5a 05             	add    %bl,0x5(%rdx)
    3f5a:	00 00                	add    %al,(%rax)
    3f5c:	00 00                	add    %al,(%rax)
    3f5e:	00 00                	add    %al,(%rax)
    3f60:	f9                   	stc
    3f61:	ff                   	(bad)
    3f62:	ff 6f 00             	ljmp   *0x0(%rdi)
    3f65:	00 00                	add    %al,(%rax)
    3f67:	00 03                	add    %al,(%rbx)
	...

Disassembly of section .got:

0000000000003fc0 <.got>:
	...

Disassembly of section .got.plt:

0000000000003fe8 <.got.plt>:
    3fe8:	e0 3d                	loopne 4027 <__cxa_finalize@plt+0x2fb7>
	...
    3ffe:	00 00                	add    %al,(%rax)
    4000:	36 10 00             	ss adc %al,(%rax)
    4003:	00 00                	add    %al,(%rax)
    4005:	00 00                	add    %al,(%rax)
    4007:	00 46 10             	add    %al,0x10(%rsi)
    400a:	00 00                	add    %al,(%rax)
    400c:	00 00                	add    %al,(%rax)
    400e:	00 00                	add    %al,(%rax)
    4010:	56                   	push   %rsi
    4011:	10 00                	adc    %al,(%rax)
    4013:	00 00                	add    %al,(%rax)
    4015:	00 00                	add    %al,(%rax)
    4017:	00 66 10             	add    %ah,0x10(%rsi)
    401a:	00 00                	add    %al,(%rax)
    401c:	00 00                	add    %al,(%rax)
	...

Disassembly of section .data:

0000000000004020 <.data>:
	...
    4028:	28 40 00             	sub    %al,0x0(%rax)
    402b:	00 00                	add    %al,(%rax)
    402d:	00 00                	add    %al,(%rax)
	...

Disassembly of section .bss:

0000000000004030 <.bss>:
	...

Disassembly of section .comment:

0000000000000000 <.comment>:
   0:	47                   	rex.RXB
   1:	43                   	rex.XB
   2:	43 3a 20             	rex.XB cmp (%r8),%spl
   5:	28 44 65 62          	sub    %al,0x62(%rbp,%riz,2)
   9:	69 61 6e 20 31 32 2e 	imul   $0x2e323120,0x6e(%rcx),%esp
  10:	32 2e                	xor    (%rsi),%ch
  12:	30 2d 31 34 29 20    	xor    %ch,0x20293431(%rip)        # 20293449 <__cxa_finalize@plt+0x202923d9>
  18:	31 32                	xor    %esi,(%rdx)
  1a:	2e 32 2e             	cs xor (%rsi),%ch
  1d:	30 00                	xor    %al,(%rax)
