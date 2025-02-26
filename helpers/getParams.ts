const clearGetParams = () => {
    const router = useRouter();

    const { email, phone } = router.currentRoute.value.query;

    if (email && phone) {
        router.push(router.currentRoute.value.fullPath);
    }
}

export { clearGetParams };
