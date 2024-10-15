#!/bin/bash
{
  sleep 0.5
  echo "A"
  sleep 0.1
  echo "A"
  sleep 0.1
  echo "B"
  sleep 0.1
  echo "D"
  sleep 0.1
  echo "C"
  sleep 0.1
  echo "D"
  sleep 0.1
  echo "C"
  sleep 0.1
  echo "C"
  sleep 0.1
  echo "D"
  sleep 0.1
  echo "A"
  sleep 0.1
  echo "C"
  sleep 0.1
  echo "A"
  sleep 0.1
  echo "B"
  sleep 0.1
  echo "C"
  sleep 0.1
  echo "C"
  sleep 0.1
  echo "B"
  sleep 0.1
  echo "D"
  sleep 0.1
  echo "A"
} | nc 405.trainoi.com 27604





   A  \n  A \n  B  \n  D  \n  C  \n  D    \n C  \n  C  \n  D\n    A\n   C\n    A\n   B\n   C\n   C\n   B\n   D\n    A

printf "A\n  A \n  B  \n  D  \n  C  \n  D    \n C  \n  C  \n  D\n    A\n   C\n    A\n   B\n   C\n   C\n   B\n   D\n    A\n" | nc 405.trainoi.com 27604
