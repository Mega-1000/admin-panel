const ajaxPost = async (data, url = './', isJson = false) => {
    
    let resData = null;

    await $.post(url, data).done(res => resData = isJson ? JSON.parse(res) : res)
    .fail(err => {
        console.log(err);
        $('.loader-2').removeClass('loader-2');
        if(typeof toastr != 'undefined') toastr.error('Błąd systemowy, spróbuj później.');
    });
    
    return resData;
}

const ajaxFormData = async (formData, url = './', isJson = false) => {

    let resData = null;

    await $.ajax({
        url: url,
        type: 'POST',
        contentType: false,
        processData: false,
        data: formData
    }).done(res => resData = isJson ? JSON.parse(res) : res)
    .fail(err => {
        console.log(err);
        $('.loader-2').removeClass('loader-2');
        if(typeof toastr != 'undefined') toastr.error('Błąd systemowy, spróbuj później.');
    });

    return resData;
}

function b64toBlob(b64Data, contentType) {
    contentType = contentType || '';
    const sliceSize = 512;

    const byteCharacters = atob(b64Data);
    const byteArrays = [];

    for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
        const slice = byteCharacters.slice(offset, offset + sliceSize);

        const byteNumbers = new Array(slice.length);
        for (let i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        const byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    const blob = new Blob(byteArrays, {type: contentType});
    return blob;
}

const saveFileAs = (fileName, content, contentType) => {

    const blob = b64toBlob(content, contentType);
    const URLObject = window.URL.createObjectURL(blob);

    const downloadLink = document.createElement('a');
    downloadLink.download = fileName;
    downloadLink.href = URLObject;
    downloadLink.style.display = 'none';

    window.document.body.appendChild(downloadLink);
    
    downloadLink.click();
}

// pass new Date() for get DateTime format
const getCurrentDateTime = date => {

    const hour = date.getHours();
    date.setUTCHours(hour);
    const currentDateTime = date.toISOString().replace('T', ' ').slice(0, -5);
    
    return currentDateTime;
};

// open url in new window trick the popup window blocking
const openInNewTab = url => {

    let anchorEl = document.createElement('a');

    Object.assign(anchorEl, {
      target: '_blank',
      rel: 'noopener noreferrer',
      href: url,
    }).click();
}
