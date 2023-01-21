$(function() {
    $('.allegro-chat-icon-wrapper').click(async () => {
        let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
        width=600,height=300,left=100,top=100`;

        const data = {

        };

        const url = 'api/allegro/listUserThreads';

        await ajaxPost(data, url);

        // const chatWindow = open('about:blank', 'allegroChat', params);
        // let html = `<div style="font-size:30px">Welcome!</div>`;
        // chatWindow.document.body.insertAdjacentHTML('afterbegin', html);
    });
});