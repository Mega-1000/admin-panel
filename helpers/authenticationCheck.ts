import { loginUser, loginFromEmail } from "~/helpers/loginUser";
import { clearGetParams } from "~/helpers/getParams";

const checkIfUserIsLoggedIn = async (message: string) => {
    const { $shopApi: shopApi } = useNuxtApp();

    try {
        const { data: user } = await shopApi.get('/api/user');

        return user;
    } catch (e) {
        await loginFromGetParams(true, message);
    }
}

const loginFromGetParams = async (redirect: boolean, message: string = 'Ta strona jest dostępna tylko dla zalogowanych użytkowników') => {
    const router = useRouter();
    const { $shopApi: shopApi } = useNuxtApp();

    let credentials: string = router.currentRoute.value.query.credentials as string;

    let email = credentials?.split(':')[0];
    let phone = credentials?.split(':')[1];

    if (!credentials) {
        try {
            await shopApi.get('/api/user');
        } catch (e) {
            setTimeout(() => {
                return redirectToLogin(message);
            }, 500);
        }
    }
    else if (email && !phone) handleOnlyEmail(email);
    else if (email && phone) {
        if (phone.startsWith('48')) phone = phone.slice(2);
        tryLoginUser(email, phone, redirect, message);
    }
}

const redirectToLogin = (message: string = 'Ta strona jest dostępna tylko dla zalogowanych użytkowników') => {
    const router = useRouter();
    clearGetParams();

    return router.push(`/login?redirect=${router.currentRoute.value.fullPath}&message=${message}`);
}

const tryLoginUser = async (email: string, phone: string, redirect: boolean, message: string) => {
    try {
        await clearGetParams();
        await loginUser(email.toString(), phone.toString());
    } catch (e) {
        redirectToLogin(message);
    }
}

const handleOnlyEmail = (email: string) => {
    const login = loginFromEmail(email);

    if (!login) {
        window.location.href = `/fill-phone-number?email=${email}`;
        return;
    }

    return;
}


export { checkIfUserIsLoggedIn, loginFromGetParams };
