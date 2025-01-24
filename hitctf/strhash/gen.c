#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>

uint64_t str_hash(char *s, uint64_t mod) {
    uint64_t res = 0x114514;
    for(; *s; s++) {
        res *= 233;
        res += *s;
        res %= mod;
    }
    return res;
}

int main(void) {
    char *flag = getenv("FLAG");

    for(int x=0; x<=100; x++) {
        uint64_t mod = rand();
        printf("%lu %lu\n", mod, str_hash(flag, mod));
    }

    return 0;
}
