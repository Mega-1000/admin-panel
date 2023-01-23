const ajaxPost = async (data, url = './', isJson = false) => {
    
    let resData = null;

    await $.post(url, data).done(res => resData = isJson ? JSON.parse(res) : res)
    .fail(err => {
        console.log(err?.responseJSON?.message || 'Something wrong');
        $('.loader-2').removeClass('loader-2');
        toastr.error('Błąd systemowy, spróbuj później.');
    });
    
    return resData;
}