
c=11850797596095451670524864488046085367812828367468720385501401042627802035427938560866042101544712923470757782908521283827297125349504897418356898752774318846698532487439368216818306352553082800908866174488983776084101115047054799618258909847935672497139557595959270012943240666681053544905262111921321629682394432293381001209674417203517322559283298774214341100975920287314509947562597521988516473281739331823626676843441511662000240327706777269733836703945274332346982187104319993337626265180132608256601473051048047584429295402047392826197446200263357260338332947498385907066370674323324146485465822881995994908925
n=21318014445451076173373282785176305352774631352746325570797607376133429388430074045541507180590869533728841479322829078527002230672051057531691634445544608584952008820389785877589775003311007782211153558201413379523215950193011250189319461422835303446888969202767656215090179505169679429932715040614611462819788222032915253996376941436179412296039698843901058781175173984980266474602529294294210502556931214075073722598225683528873417278644194278806584861250188304944748756498325965302770207316134309941501186831407953950259399116931502886169434888276069750811498361059787371599929532460624327554481179566565183721777
x=4780454330598494796755521994676122817049316484524449315904838558624282970709853419493322324981097593808974378840031638879097938241801612033487018497098140216369858849215655128326752931937595077084912993941304190099338282258345677248403566469008681644014648936628917169410836177868780315684341713654307395687505633335014603359767330561537038768638735748918661640474253502491969012573691915259958624247097465484616897537609020908205710563729989781151998857899164730749018285034659826333237729626543828084565456402192248651439973664388584573568717209037035304656129544659938260424175198672402598017357232325892636389317
y=9819969459625593669601382899520076842920503183309309803192703938113310555315820609668682700395783456748733586303741807720797250273398269491111457242928322099763695038354042594669354762377904219084248848357721789542296806917415858628166620939519882488036571575584114090978113723733730014540463867922496336721404035184980539976055043268531950537390688608145163366927155216880223837210005451630289274909202545128326823263729300705064272989684160839861214962848466991460734691634724996390718260697593087126527364129385260181297994656537605275019190025309958225118922301122440260517901900886521746387796688737094737637604
e = 65537

R = Integers(n)

P.<a, b, p, q> = PolynomialRing(Integers(n))

f1 = a*p + q

f2 = p + b*q

f3 = p*q

I = Ideal([f1 - x, f2 - y, f3 - n])
B = I.groebner_basis()

print(B)

g = B[-1]

z = ZZ(g.coefficient({q: 1}))
assert g.constant_coefficient() == R(-y)

_, (z1, _), (z2, _) = list(g)
z1 = ZZ(z1)
z2 = ZZ(z2)

S = 2^1024

for p_upper_bits in range(16):
	p_upper = p_upper_bits << 1020
	for q_upper_bits in range(16):
		q_upper = q_upper_bits << 1020
		M = matrix(ZZ, [[S, -1, 0, 0], [S*z1, 0, -1, 0], [S*(z2 + p_upper + q_upper*z1), 0, 0, S], [S*n, 0, 0, 0]])
		B = M.LLL()
		for b in B:
			if b[-1] == S:
				if b[1] < 0:
					b *= -1

				p_guess = b[1] + p_upper
				q_guess = b[2] + q_upper
				if p_guess * q_guess == n:
					d = pow(e, -1, (p_guess - 1)*(q_guess - 1))
					print(int(pow(c, d, n)).to_bytes(1024//8, 'big'))
					exit()

# b'flag{3z_r5a_15_r34lly_345y_w1sh_u_c0uld_g3t_f14g}\xcb\xdfr\x1f\xf6E\x04 \x83_U\xe5I\xef\x82\xde\x8a\xe4p\x95R*\x89.1<\xc2\x9d[OU#\x19 \x0f\x0eQ\xe9\xaa\x83\xb1\xfeG8\x99yB`\x04\xec\xa9\x13\x85@\xb1R\x97h\xb4\xfd\xaa]py\xdf\x87\x08\xaf\xf6\xdaf\x0b\x00\xf2\x0c\xc9l8\x03'