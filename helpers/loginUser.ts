import { setCookie } from "~/helpers/authenticator";
import { clearGetParams } from "~/helpers/getParams";
const loginUser = async (email: string, password: string) => {
    const { $shopApi: shopApi } = useNuxtApp();
    const router = useRouter();
    const config = useRuntimeConfig().public;

    const params = {
        grant_type: "password",
        client_id: config.AUTH_CLIENT_ID,
        client_secret: config.AUTH_CLIENT_SECRET,
        username: email,
        password: password,
        scope: "",
    };

    const res = await shopApi.post("oauth/token", params);
    setCookie(res.data);

    router.go(0);
    clearGetParams();
}

const loginFromEmail = async (email: string) => {
    const { $shopApi: shopApi } = useNuxtApp();
    const router = useRouter();

    try {
        const res = await shopApi.post(`api/oauth/token/from-email/${email}`);

        setCookie(res.data);
        router.go(0);
    } catch (e) {
        return false;
    }

    return true;
}

export {
    loginUser,
    loginFromEmail
};
