import Link from "next/link";

export default function Home() {
  return (
      <>
        <h1 className="text-2xl font-bold">Home</h1>
        <p>Welcome to SCTF 2024!Have a Good time!!</p>
        <p>Play a Game <Link href="/play">here</Link>.</p>
      </>
  );
}
