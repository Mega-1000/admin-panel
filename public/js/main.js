$(function() {
    // init Allegro Chat
    const ajaxPath = '/admin/';
    const iconWrapperChat = $('.allegro-chat-icon-wrapper');
    const iconCounterChat = $('.allegro-chat-icon-counter');

    const chatPaths = {
        checkUnreadedThreads: 'allegro/checkUnreadedThreads',
        bookThread: 'allegro/bookThread',
        getMessages: 'allegro/getMessages/',
        messagesPreview: 'allegro/messagesPreview/',
    };

    const allegroChatInitializer = new AllegroChatInitializer(iconWrapperChat, iconCounterChat, ajaxPath, chatPaths, 'chat');

    // init Disputes Chat
    const iconWrapperDisputes = $('.allegro-dispute-icon-wrapper');
    const iconCounterDisputes = $('.allegro-dispute-icon-counter');

    const disputesPaths = {
        checkUnreadedThreads: 'allegro/getNewPendingDisputes',
        bookThread: 'allegro/bookDispute',
    };

    const allegroDisputesInitializer = new AllegroChatInitializer(iconWrapperDisputes, iconCounterDisputes, ajaxPath, disputesPaths, 'disputes');

    // init Orders Chat
    const iconWrapperOrders = $('.need-support-icon-wrapper');
    const iconCounterOrders = $('.need-support-icon-counter');

    const ordersPaths = {
        checkUnreadedThreads: 'getNewNeedSupportOrders',
        resolveOrderNeededSupport: 'resolveOrderNeededSupport',
    };

    const allegroOrdersInitializer = new AllegroChatInitializer(iconWrapperOrders, iconCounterOrders, ajaxPath, ordersPaths, 'orders');


});
