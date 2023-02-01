$(function() {
    const iconWrapper = $('.allegro-chat-icon-wrapper');
    const iconCounter = $('.allegro-chat-icon-counter');
    const ajaxPath = this.location.pathname == '/admin' ? '/admin/' : '/admin/';

    const chatPaths = {
        checkUnreadedThreads: 'allegro/checkUnreadedThreads',
        bookThread: 'allegro/bookThread',
        getMessages: 'allegro/getMessages/',
        messagesPreview: 'allegro/messagesPreview/',
    };

    const allegroChatInitializer = new AllegroChatInitializer(iconWrapper, iconCounter, ajaxPath, chatPaths);
});
