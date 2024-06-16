CXX = clang++
CXXFLAGS = -lcgicc -static -fuse-ld=lld

all: upload.cgi readfile.cgi

upload.cgi: upload.cpp
	$(CXX) $(CXXFLAGS) -o html/cgi-bin/upload.cgi $^; chmod a+x html/cgi-bin/*

readfile.cgi: readfile.cpp
	$(CXX) $(CXXFLAGS) -o html/cgi-bin/readfile.cgi $^; chmod a+x html/cgi-bin/*
