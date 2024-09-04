#include <cstdint>
#include <cstdlib>
#include <ostream>
#include <stdio.h>
#include <string.h>
#include <thread>
#include <vector>
#include <iostream>

#define ROUND 16

//S-Box 16x16
int sBox[16] =
        {
                2, 10, 4, 12,
                1, 3, 9, 14,
                7, 11, 8, 6,
                5, 0, 15, 13
        };

int reversesBox[16] =
        {
                13, 4, 0, 5,
                2, 12, 11, 8,
                10, 6, 1, 9,
                3, 15, 7, 14
        };


// 将十六进制字符串转换为 unsigned char 数组
void hex_to_bytes(const char* hex_str, unsigned char* bytes, size_t bytes_len) {
    size_t hex_len = strlen(hex_str);
    if (hex_len % 2 != 0 || hex_len / 2 > bytes_len) {
        fprintf(stderr, "Invalid hex string length.\n");
        return;
    }

    for (size_t i = 0; i < hex_len / 2; i++) {
        sscanf(hex_str + 2 * i, "%2hhx", &bytes[i]);
    }
}


// 派生轮密钥
void derive_round_key(unsigned int key, unsigned char *round_key, int length) {

    unsigned int tmp = key;
    for(int i = 0; i < length / 16; i++)
    {
        memcpy(round_key + i * 16,      &tmp, 4);   tmp++;
        memcpy(round_key + i * 16 + 4,  &tmp, 4);   tmp++;
        memcpy(round_key + i * 16 + 8,  &tmp, 4);   tmp++;
        memcpy(round_key + i * 16 + 12, &tmp, 4);   tmp++;
    }
}


// 比特逆序
void reverseBits(unsigned char* state) {
    unsigned char temp[16];
    for (int i = 0; i < 16; i++) {
        unsigned char byte = 0;
        for (int j = 0; j < 8; j++) {
            byte |= ((state[i] >> j) & 1) << (7 - j);
        }
        temp[15 - i] = byte;
    }
    for (int i = 0; i < 16; i++) {
        state[i] = temp[i];
    }
}

void sBoxTransform(unsigned char* state) {
    for (int i = 0; i < 16; i++) {
        int lo = sBox[state[i] & 0xF];
        int hi = sBox[state[i] >> 4];
        state[i] = (hi << 4) | lo;
    }
}

void reversesBoxTransform(unsigned char* state) {
    for (int i = 0; i < 16; i++) {
        int lo = reversesBox[state[i] & 0xF];
        int hi = reversesBox[state[i] >> 4];
        state[i] = (hi << 4) | lo;
    }
}

void leftShiftBytes(unsigned char* state) {
    unsigned char temp[16];
    for (int i = 0; i < 16; i += 4) {
        temp[i + 0] = state[i + 2] >> 5 | (state[i + 1] << 3);
        temp[i + 1] = state[i + 3] >> 5 | (state[i + 2] << 3);
        temp[i + 2] = state[i + 0] >> 5 | (state[i + 3] << 3);
        temp[i + 3] = state[i + 1] >> 5 | (state[i + 0] << 3);
    }
    for (int i = 0; i < 16; i++)
    {
        state[i] = temp[i];
    }
}

void rightShiftBytes(unsigned char* state) {
    unsigned char temp[16];
    for (int i = 0; i < 16; i += 4) {
        temp[i + 0] = state[i + 2] << 5 | (state[i + 3] >> 3);
        temp[i + 1] = state[i + 3] << 5 | (state[i + 0] >> 3);
        temp[i + 2] = state[i + 0] << 5 | (state[i + 1] >> 3);
        temp[i + 3] = state[i + 1] << 5 | (state[i + 2] >> 3);
    }
    for (int i = 0; i < 16; i++)
    {
        state[i] = temp[i];
    }
}

// 轮密钥加
void addRoundKey(unsigned char* state, unsigned char* roundKey, unsigned int round) {
    for (int i = 0; i < 16; i++) {
        for (int j = 0; j < 8; j++) {
            state[i] ^= ((roundKey[i + round * 16] >> j) & 1) << j;
        }
    }
}

// 加密函数
void encrypt(unsigned char* password, unsigned int key, unsigned char* ciphertext) {
    unsigned char roundKeys[16 * ROUND] = {}; //

    // 生成轮密钥
    derive_round_key(key, roundKeys, 16 * ROUND);

    // 初始状态为16字节的口令
    unsigned char state[16]; // 初始状态为16字节的密码
    memcpy(state, password, 16); // 初始状态为密码的初始值

    // 迭代加密过程
    for (int round = 0; round < ROUND; round++)
    {
        reverseBits(state);
        sBoxTransform(state);
        leftShiftBytes(state);
        addRoundKey(state, roundKeys, round);
    }

    memcpy(ciphertext, state, 16);
}

void decrypt(unsigned char* password, unsigned int key, unsigned char* ciphertext) {
    unsigned char roundKeys[16 * ROUND] = {}; //

    // 生成轮密钥
    derive_round_key(key, roundKeys, 16 * ROUND);

    unsigned char state[16];
    memcpy(state, ciphertext, 16);

    // 迭代加密过程
    for (int round = ROUND - 1; round >= 0; round--)
    {
        addRoundKey(state, roundKeys, round);
        rightShiftBytes(state);
        reversesBoxTransform(state);
        reverseBits(state);
    }

    memcpy(password, state, 16);
}

void try_decrypt(unsigned int start, unsigned int stop) {
    unsigned char ciphertext[] = {0x99, 0xF2, 0x98, 0x0A, 0xAB, 0x4B, 0xE8, 0x64, 0x0D, 0x8F, 0x32, 0x21, 0x47, 0xCB, 0xA4, 0x09};
    unsigned char password[16];
    for (unsigned int key = stop; key > start; --key) {
        // if(key % 10000 == 0)
            // printf("%u\n", key);
        decrypt(password, key, ciphertext);
        const char *pwd = "pwd:";
        if (!memcmp(password, pwd, 4)){
            std::cout << key << std::endl;
            std::cout << password << std::endl;
            // return;
        }
    }
}

int main(int argc, char *argv[]) {
    unsigned num_threads = 14;
    unsigned int step = UINT32_MAX / 18;
    std::vector<std::thread> threads;
    for (int i = 0; i < num_threads; ++i) {
        unsigned int start = i * step, stop = (i + 1) * step;
        threads.emplace_back(try_decrypt, start, stop);
    }
    for (int i = 0; i < num_threads; ++i)
        threads[i].join();
}