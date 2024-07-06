//PollardRho
#include <bits/stdc++.h>
using namespace std;
typedef long long ll;
ll gcd(ll a, ll b) {
    return b ? gcd(b, a % b) : a;
}
ll PollardRho(ll n, ll c) {
    ll x = rand() % n, y = x, d = 1;
    auto f = [&](ll x) { return (x * x + c) % n; };
    while (d == 1) {
        x = f(x), y = f(f(y));
        d = gcd(abs(x - y), n);
    }
    return d;
}
void factorize(ll n, vector<ll> &v) {
    if (n == 1) return;
    if (n % 2 == 0) {
        v.push_back(2);
        factorize(n / 2, v);
        return;
    }
    if (isprime(n)) {
        v.push_back(n);
        return;
    }
    ll p = n;
    while (p >= n) p = PollardRho(n, rand() % (n - 1) + 1);
    factorize(p, v);
    factorize(n / p, v);
}
int main() {
    ll n;
    n = 104765768221225848380273603921218042896496091723683489832860494733817042387427987244507704052637674086899990536096984680534816330245712225302233334574349506189442333792630084535988347790345154447062755551340749218034086168589615547612330724516560147636445207363257849894676399157463355106007051823518400959497
    vector<ll> v;
    factorize(n, v);
    sort(v.begin(), v.end());
    for (ll x : v) cout << x << ' ';
    cout << endl;
    return 0;
}
