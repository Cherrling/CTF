#include<bits/stdc++.h>
using namespace std;
int key[64];
int lis[1000] = {0, 2, 1, 1, 3, 1, 1, 3, 2, 1, 4, 1, 2, 3, 1, 1, 1, 2, 1, 1, 2, 2, 2, 1, 3, 1, 6, 1, 1, 4, 1, 1, 1, 1, 1, 1, 1, 1, 2, 1, 2, 1, 1, 3, 3, 2, 1, 1, 3, 1, 1, 1, 3, 4, 1, 3, 1, 2, 2, 4, 2, 5, 1, 1, 1, 3, 2, 1, 4, 2, 2, 1, 2, 1, 3, 1, 1, 1, 1, 1, 2, 3, 1, 2, 1, 1, 1, 1, 3, 4, 1, 2, 2, 4, 2, 5, 1, 2, 1, 2, 2, 1, 4, 1, 2, 1, 2, 1, 1, 1, 1, 1, 1, 2, 2, 1, 4, 3, 1, 2, 1, 3, 1, 3, 3, 2, 1, 3, 1, 6, 2, 1, 1, 2, 1, 2, 1, 3, 1, 1, 2, 1, 2, 1, 1, 2, 1, 2, 2, 2, 3, 1, 1, 4, 1, 3, 1, 1, 1, 2, 1, 1, 2, 4, 1, 1, 5, 2, 4, 2, 2, 1, 1, 1, 2, 1, 1, 1, 2, 1, 3, 3, 1, 1, 1, 1, 1, 2, 1, 2, 3, 1, 1, 2, 1, 1, 2, 1, 2, 1, 2, 1, 1, 1, 2, 5, 1, 1, 1, 3, 1, 1, 2, 3, 1, 2, 2, 2, 1, 3, 3, 1, 1, 2, 1, 1, 4, 3, 1, 3, 4, 1, 1, 1, 2, 1, 3, 1, 6, 1, 2, 1, 1, 3, 2, 3, 1, 2, 2, 1, 3, 2, 1, 2, 2, 2, 3, 3, 3, 1, 1, 2, 4, 1, 1, 1, 1, 1, 4, 2, 1, 4, 1, 2, 3, 2, 1, 1, 1, 2, 1, 1, 1, 2, 1, 3, 2, 1, 2, 1, 1, 1, 4, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 3, 2, 4, 2, 1, 4, 2, 4, 2, 2, 3, 1, 2, 2, 2, 1, 3, 3, 1, 2, 1, 1, 1, 1, 3, 3, 1, 3, 1, 1, 1, 1, 3, 1, 1, 4, 2, 5, 2, 1, 3, 1, 1, 2, 3, 1, 2, 2, 1, 1, 1, 1, 1, 1, 3, 1, 2, 1, 3, 1, 2, 3, 4, 4, 3, 2, 4, 2, 1, 4, 2, 4, 1, 2, 1, 3, 1, 2, 1, 1, 1, 3, 2, 1, 2, 2, 2, 3, 3, 1, 2, 1, 3, 1, 1, 1, 2, 1, 3, 4, 2, 1, 4, 1, 2, 1, 2, 2, 2, 1, 1, 2, 1, 1, 2, 2, 2, 1, 4, 2, 1, 4, 1, 1, 1, 1, 2, 4, 4, 3, 2, 4, 2, 1, 1, 1, 1, 1, 1, 1, 4, 2, 2, 3, 1, 1, 1, 2, 1, 3, 1, 4, 1, 2, 4, 1, 2, 3, 4, 1, 3, 1, 1, 1, 2, 4, 1, 1, 1, 4, 1, 1, 4, 2, 1, 4, 2, 2, 1, 1, 1, 1, 1, 2, 3, 2, 1, 4, 3, 3, 4, 4, 3, 2, 4, 2, 1, 1, 3, 2, 4, 1, 1, 2, 3, 1, 1, 1, 2, 2, 1, 1, 1, 1, 3, 1, 1, 1, 4, 3, 3, 1, 1, 2, 1, 1, 1, 1, 3, 1, 1, 4, 2, 5, 1, 1, 4, 2, 1, 1, 1, 1, 1, 1, 2, 2, 2, 1, 1, 2, 1, 2, 1, 2, 4, 3, 1, 1, 1, 1, 3, 4, 3, 1, 1, 4, 1, 6, 2, 1, 1, 1, 3, 1, 1, 3, 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 1, 4, 3, 1, 1, 5, 4, 1, 2, 2, 4, 1, 6, 1, 2, 1, 1, 3, 1, 4, 1, 2, 1, 2, 1, 1, 1, 1, 4, 2, 2, 3, 1, 2, 3, 1, 3, 4, 1, 1, 3, 4, 2, 5, 1, 1, 1, 3, 2, 2, 3, 2, 1, 2, 2, 2, 2, 3, 1, 2, 1, 3, 3, 3, 1, 1, 2, 1, 3, 3, 1, 1, 4, 2, 5, 2, 4, 1, 2, 4, 1, 2, 1, 2, 1, 1, 1, 2, 3, 1, 2, 4, 1, 1, 4, 4, 1, 1, 2, 3, 2, 4, 2, 5, 1, 2, 1, 2, 1, 1, 2, 3, 1, 2, 1, 2, 1, 1, 3, 1, 1, 2, 1, 2, 3, 1, 1, 1, 3, 4, 1, 1, 2, 1, 1, 1, 2, 4, 2, 1, 1, 3, 1, 2, 1, 2, 2, 2, 1, 2, 2, 1, 1, 1, 2, 1, 3, 1, 1, 2, 1, 2, 3, 1, 1, 1, 3, 4, 1, 1, 2, 3, 1, 2, 3, 1, 6, 2, 1, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 1, 2, 1, 1, 1, 4, 2, 1, 4, 1, 2, 3, 1, 1, 2, 1, -1};
int msg[1000];


int st, presum, len = 1000;

void printkey(int s)
{
    for(int i = 0; i < 64; ++i)
        printf("%d", key[i]);
    printf("\n");
    for(int i = st; i < s; ++i)
        printf("%d", msg[i]);
    printf("\n");
}

/*
cipher[i] = c:
m[i] == m[i+1] -> w[x] == w[x+c] == m[i] && w[x+1..x+c-1] != m[i]
m[i] != m[i+1] -> w[x..x+c-1] == m[i] && w[x+c] == m[i+1]
*/
int dfs(int x, int d)
{
    if(lis[d] == -1)
    {
        printf("success.\n");
        printkey(d);
        return 1;
    }
    int nxt_x = x + lis[d], overflow = 0;
    int orig_key[64];
    memcpy(orig_key, key, sizeof(key));
    overflow = nxt_x >= 64;

    // if(d <= 35 && x <= lis[d-1])
    {
    // printf("x = %d, d = %d, nxt_x = %d, lis[d] = %d, key = ", x, d, nxt_x, lis[d]);
    // printkey(d);
    // system("pause");
    }


    // test: msg[d] == msg[d-1]
    // m[i] == m[i+1] -> w[x] == w[x+c] == m[i] && w[x+1..x+c-1] != m[i]
    msg[d] = msg[d-1];
    if(!overflow)
    {
        // printf("A\n");
        for(int i = x + 1; i < nxt_x; ++i)
        {
            if(key[i] == msg[d])
                goto fail_attempt_1;
            key[i] = !msg[d];
        }
        if(x >= 0 && key[x] != -1 && key[x] != msg[d])
            goto fail_attempt_1;
        if(key[nxt_x] != -1 && key[nxt_x] != msg[d])
            goto fail_attempt_1;
        key[x] = msg[d];
        key[nxt_x] = msg[d];
        dfs(nxt_x, d+1);
        fail_attempt_1:;
        memcpy(key, orig_key, sizeof(key));
    }
    else
    {
        // printf("A\n");
        int new_x = nxt_x - 64;
        for(int i = x + 1; i < 64; ++i)
        {
            if(key[i] == msg[d])
                goto fail_attempt_1;
            key[i] = !msg[d];
        }
        for(int i = 0; i < new_x; ++i)
        {
            if(key[i] == msg[d])
                goto fail_attempt_1;
            key[i] = !msg[d];
        }
        if(x >= 0 && key[x] != -1 && key[x] != msg[d])
            goto fail_attempt_1;
        if(key[new_x] != -1 && key[new_x] != msg[d])
            goto fail_attempt_1;
        key[x] = msg[d];
        key[new_x] = msg[d];
        dfs(new_x, d+1);
        memcpy(key, orig_key, sizeof(key));
    }

    // test: msg[d] != msg[d-1]
    // m[i] != m[i+1] -> w[x..x+c-1] == m[i] && w[x+c] == m[i+1]
    msg[d] = !msg[d-1];
    if(!overflow)
    {
        // printf("B\n");
        for(int i = x; i < nxt_x; ++i)
        {
            if(key[i] != -1 && key[i] == msg[d])
                goto fail_attempt_2;
            key[i] = msg[d - 1];
        }
        if(key[nxt_x] != -1 && key[nxt_x] != msg[d])
            goto fail_attempt_2;
        key[nxt_x] = msg[d];
        dfs(nxt_x, d+1);
        fail_attempt_2:;
        memcpy(key, orig_key, sizeof(key));
    }
    else
    {
        // printf("B\n");
        int new_x = nxt_x - 64;
        for(int i = x; i < 64; ++i)
        {
            if(key[i] != -1 && key[i] == msg[d])
                goto fail_attempt_2;
            key[i] = msg[d - 1];
        }
        for(int i = 0; i < new_x; ++i)
        {
            if(key[i] != -1 && key[i] == msg[d])
                goto fail_attempt_2;
            key[i] = msg[d - 1];
        }
        if(key[new_x] != -1 && key[new_x] != msg[d])
            goto fail_attempt_2;
        key[new_x] = msg[d];
        dfs(new_x, d+1);
        memcpy(key, orig_key, sizeof(key));
    }
    return 0;
}

int main()
{
    for(int i = 0; i < 64; ++i) key[i] = -1;
    int pres = 0;
    for(int a = 1; a <= 700; ++a)
    {
        int sum = 0;
        for(int i = a; sum <= 64; ++i)
        {
            sum += lis[i];
            if(sum > 64)
            {
                printf("%d~%d => %d, len = %d\n", a, i, sum, i - a);
                if(i - a < len)
                {
                    len = i - a;
                    presum = pres;
                    st = a;
                }
            }
        }
        pres += lis[a];
    }
    printf("minimum subseg sum: %d~%d => %d\n", st, st+len, presum);
    msg[st - 1] = 0;
    dfs(-1, st);
    printf("done.\n");
    getchar();
    while(1);
    return 0;
}
