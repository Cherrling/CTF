

import {redirect} from "next/navigation";

export default function Success(){
    return (
        <>
        <div>Congratulations! CTFer!</div>
        <form
            action={async () => {
                "use server";
                redirect("/play");
            }}
        >
        <button type="submit">Let us Play Again!!</button>
        </form>
    </>
    )
}