_BYTE *__fastcall sub_1169(__int64 a1, int a2)
{
  _BYTE *v3; // [rsp+10h] [rbp-10h]
  int i; // [rsp+1Ch] [rbp-4h]

  v3 = malloc(a2 + 1);
  for ( i = 0; i < a2; ++i )
    v3[i] = (*(_BYTE *)(i + 1LL + a1) ^ *(_BYTE *)(i + a1)) + 33;
  v3[i] = 0;
  return v3;
}

__int64 __fastcall main(int a1, char **a2, char **a3)
{
  int v3; // eax
  _BYTE *v5; // [rsp+10h] [rbp-10h]
  int i; // [rsp+1Ch] [rbp-4h]

  if ( a1 == 2 )
  {
    v3 = strlen(a2[1]);
    v5 = sub_1169((__int64)a2[1], v3);
    for ( i = 0; v5[i]; ++i )
      printf("%02x", (unsigned __int8)v5[i]);
  }
  else
  {
    puts("usage: ./encrypt <flag>");
  }
  return 0LL;
}

2b2e273d3b797e75272326766a6f7222716f3a727e7c6f362e28746d697d2c2272752876752172776b9e