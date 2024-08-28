# # SageMath script to solve the discrete logarithm problem using Pollard's Kangaroo Algorithm
# # with a custom elliptic curve addition formula.

# p = 10297529403524403127640670200603184608844065065952536889
# a = 2
# G = (8879931045098533901543131944615620692971716807984752065, 4106024239449946134453673742202491320614591684229547464)
# Q = (6784278627340957151283066249316785477882888190582875173, 6078603759966354224428976716568980670702790051879661797)

# def add_THcurve(P, Q, p, a):
#     """
#     Add two points P and Q on the custom elliptic curve.
#     :param P: First point (x1, y1)
#     :param Q: Second point (x2, y2)
#     :param p: Modulus
#     :param a: Curve parameter a
#     :return: Sum of P and Q
#     """
#     if P == (0, 0):
#         return Q
#     if Q == (0, 0):
#         return P
#     x1, y1 = P
#     x2, y2 = Q
#     inv_denom = pow(a * x1 * y1 * x2 ** 2 - y2, -1, p)
#     x3 = (x1 - y1 ** 2 * x2 * y2) * inv_denom % p
#     y3 = (y1 * y2 ** 2 - a * x1 ** 2 * x2) * inv_denom % p
#     return (x3, y3)

# def mul_THcurve(n, P, p, a):
#     """
#     Multiply a point P by an integer n using the custom elliptic curve multiplication.
#     :param n: Scalar multiplier
#     :param P: Point (x, y)
#     :param p: Modulus
#     :param a: Curve parameter a
#     :return: Resulting point after scalar multiplication
#     """
#     R = (0, 0)
#     while n > 0:
#         if n % 2 == 1:
#             R = add_THcurve(R, P, p, a)
#         P = add_THcurve(P, P, p, a)
#         n = n // 2
#     return R

# def pollards_kangaroo(G, Q, p, a, max_steps):
#     """
#     Pollard's Kangaroo Algorithm to solve the discrete logarithm problem.
#     :param G: Base point on the curve
#     :param Q: Target point
#     :param p: Modulus
#     :param a: Curve parameter a
#     :param max_steps: Maximum number of steps to try
#     :return: The discrete logarithm m if found
#     """
#     # Initialize kangaroos
#     xA, xB = randint(1, p-1), randint(1, p-1)
#     A, B = mul_THcurve(xA, G, p, a), mul_THcurve(xB, G, p, a)
#     alpha, beta = randint(1, p-1), randint(1, p-1)
#     kangarooA, kangarooB = G, G

#     for i in range(max_steps):
#         # Move the kangaroos
#         A = add_THcurve(A, G, p, a)
#         B = add_THcurve(B, G, p, a)
        
#         # Check for collision
#         if A == B:
#             k = (xB - xA) % (p-1)
#             if k != 0:
#                 k_inv = inverse_mod(k, p-1)
#                 m = (xB - k * xA) % (p-1)
#                 return m

#     return None

# # Run Pollard's Kangaroo Algorithm
# max_steps = 1000000  # Maximum number of steps to try
# m = pollards_kangaroo(G, Q, p, a, max_steps)

# if m is not None:
#     print(f"Found m: {m}")
# else:
#     print("No solution found within the given steps.")






# # SageMath script to solve the discrete logarithm problem using Baby-step Giant-step algorithm
# # with the given custom elliptic curve.

# p = 10297529403524403127640670200603184608844065065952536889
# a = 2
# G = (8879931045098533901543131944615620692971716807984752065, 4106024239449946134453673742202491320614591684229547464)
# Q = (6784278627340957151283066249316785477882888190582875173, 6078603759966354224428976716568980670702790051879661797)

# def add_THcurve(P, Q, p, a):
#     """
#     Add two points P and Q on the custom elliptic curve.
#     :param P: First point (x1, y1)
#     :param Q: Second point (x2, y2)
#     :param p: Modulus
#     :param a: Curve parameter a
#     :return: Sum of P and Q
#     """
#     if P == (0, 0):
#         return Q
#     if Q == (0, 0):
#         return P
#     x1, y1 = P
#     x2, y2 = Q
#     inv_denom = pow(a * x1 * y1 * x2 ** 2 - y2, -1, p)
#     x3 = (x1 - y1 ** 2 * x2 * y2) * inv_denom % p
#     y3 = (y1 * y2 ** 2 - a * x1 ** 2 * x2) * inv_denom % p
#     return (x3, y3)

# def mul_THcurve(n, P, p, a):
#     """
#     Multiply a point P by an integer n using the custom elliptic curve multiplication.
#     :param n: Scalar multiplier
#     :param P: Point (x, y)
#     :param p: Modulus
#     :param a: Curve parameter a
#     :return: Resulting point after scalar multiplication
#     """
#     R = (0, 0)
#     while n > 0:
#         if n % 2 == 1:
#             R = add_THcurve(R, P, p, a)
#         P = add_THcurve(P, P, p, a)
#         n = n // 2
#     return R

# def baby_step_giant_step(g, h, p, a):
#     """
#     Solve the discrete logarithm problem using Baby-step Giant-step algorithm.
#     :param g: Base point on the curve
#     :param h: Target point
#     :param p: Modulus
#     :param a: Curve parameter a
#     :return: The discrete logarithm m if found
#     """
#     import math

#     # Calculate m as the integer square root of p
#     m = int(math.ceil(math.sqrt(p)))

#     # Baby steps: compute g^j for j in [0, m-1] and store in a dictionary
#     baby_steps = {}
#     current = (0, 0)
#     for j in range(m):
#         baby_steps[current] = j
#         current = add_THcurve(current, g, p, a)

#     # Giant steps: compute h * g^(-i*m) for i in [0, m-1] and check for matches
#     g_inv_m = mul_THcurve(-m, g, p, a)
#     current = h
#     for i in range(m):
#         if current in baby_steps:
#             # Compute discrete log m
#             j = baby_steps[current]
#             return i * m + j
#         current = add_THcurve(current, g_inv_m, p, a)

#     return None

# # Run Baby-step Giant-step Algorithm
# m = baby_step_giant_step(G, Q, p, a)

# if m is not None:
#     print(f"Found m: {m}")
# else:
#     print("No solution found.")


from Crypto.Util.number import *
from sympy import discrete_log

p = 10297529403524403127640670200603184608844065065952536889
a = 2
#Q点
x,y = (8879931045098533901543131944615620692971716807984752065, 4106024239449946134453673742202491320614591684229547464)
d=8817708809404273675545317762394593437543647288341187200
b=2023311503805954111362735874303860040942654548593039668
x1=x*b%p
y1=y
# d1=((x1)**3+y**3+1)*inverse(b*x*y, p)%p
# print(d1)
d1=4197156738236715923181334026153482564495893747641139426
print((x1**3+y1**3+1)%p==d1*x1*y1%p)
d2=inverse(3, p)*d1%p
u = (12*(d2**3-1)*inverse(d2+x1+y1,p)-9*d2**2)%p
v= 36*(y1-x1)*(d2**3-1)*inverse(d2+x1+y1,p)%p
print(u,v)
a4 = -27*d2*(d2**3+8)
a6 = 54*(d2**6-20*d2**3-8)
E=EllipticCurve(GF(p),[a4,a6])
P=E((u,v))
print(P)
x2,y2=(6784278627340957151283066249316785477882888190582875173*b, 6078603759966354224428976716568980670702790051879661797)
u = (12*(d2**3-1)*inverse(d2+x2+y2,p)-9*d2**2)%p
v= 36*(y2-x2)*(d2**3-1)*inverse(d2+x2+y2,p)%p
Q=E((u,v))
print(Q)
print(E.order())


factors, exponents = zip(*factor(E.order()))
primes = [factors[i] ^ exponents[i] for i in range(len(factors))][4:-1]
print(primes)
dlogs = []
for fac in primes:
	t = int(int(P.order()) // int(fac))
	dlog = discrete_log(t*Q,t*P,operation="+")
	dlogs += [dlog]
	print("factor: "+str(fac)+", Discrete Log: "+str(dlog)) #calculates discrete logarithm for each prime order

l = crt(dlogs,primes)
print(l)
print(long_to_bytes(l))
