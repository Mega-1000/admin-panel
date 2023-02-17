class AllegroChat {
    
    isChatWindowOpen = true;

    constructor(ajaxPath, threadId, messages, nickname, isPreview) {
        this.ajaxPath = ajaxPath;
        this.threadId = threadId;
        this.isPreview = isPreview;

        setInterval(() => {
            this.getNewMessages();
        }, 20000);
        
        // auto close window after 20 min.
        this.timeout = '';

        this.inactiveCountdown();

        this.renderChat(messages, nickname);
        // after render chat we've got char-wrapper and chat-footer
        this.wrapper = $('.allegro-chat-wrapper');
        this.footer = $('.allegro-chat-footer');
        this.footer.addClass('loader-2');
        this.getNewMessages();

        this.chatScrollDown();

        this.initListeners();
    }

    initListeners() {

        document.addEventListener("visibilitychange", () => this.handleWindowVisibilityChange());

        $('.allegro-send').on('click', () => {
            this.footer.addClass('loader-2');

            this.sendMessage();

        });
        $('body').on('click', '.allegro-attachments-item', e => {
            this.footer.addClass('loader-2');

            this.downloadAttachment(e);

            this.footer.removeClass('loader-2');
        });
        $('.allegro-close-conversation').on('click', () => {
            this.wrapper.addClass('loader-2');
            this.closeChat();
        });

    }

    handleWindowVisibilityChange() {

        if(!this.isChatWindowOpen) return false;

        const dateTime = getCurrentDateTime(new Date);

        window.localStorage.setItem('allegro_chat_last_check', dateTime);
    }

    async downloadAttachment(e) {
      const attachmentId = $(e.target).data('id');
      const attachmentName = $(e.target).text();

      const url = this.ajaxPath + 'allegro/downloadAttachment/'+attachmentId;
      const fileData = await ajaxPost({}, url);

      if(!fileData.content || !fileData.contentType) return false;
      saveFileAs(attachmentName, fileData.content, fileData.contentType);
    }
    
    makeSingleMessageTemplate(msg) {
        
        const type = msg.is_outgoing ? 'outgoing' : 'incoming';
        
        const attachments = typeof msg.attachments !== 'undefined' ? JSON.parse(msg.attachments) : [];
        const date = msg.original_allegro_date;
        const attachmentsTemplate = attachments.length > 0 && attachments.map(attachment => {
            if(attachment.status == 'UNSAFE' || attachment.status == 'EXPIRED' || !attachment.url) return ``;
            const attachmentId = attachment.url.split('/').pop();
            return `
                <div class="allegro-attachments-list">
                    <a class="allegro-attachments-item" href="javascript:;" data-id="${attachmentId}">${attachment.fileName}</a>
                </div>
            `;
        }).join('') || '';

        return `
            <div class="allegro-msg-wrapper ${type}">
                <div class="allegro-msg-header">
                    <div class="allegro-msg-type">${type === 'incoming' ? `Przychodząca` : `Wychodząca`}</div>
                    <div class="allegro-msg-date" data-date="${date}">[${date}]</div>
                    <div class="allegro-msg-consultant">
                        Konsultant: <strong>${msg?.user?.name}</strong>
                    </div>
                    ${msg.subject ? `<div class="allegro-msg-subject">
                        <strong>Temat: ${msg.subject}</strong>
                    </div>` : ``}
                </div>
                <div class="allegro-msg-content">
                    ${msg.content}
                </div>
                <div class="allegro-msg-footer">
                    ${ msg.allegro_offer_id ? `<div>
                    <a href="https://allegro.pl/oferta/${msg.allegro_offer_id}" target="_blank" class="btn allegro-msg-offer-id">Przejdź do oferty</a></div>`
                     : `` }
                    ${ msg.allegro_order_id ? `<div>
                    <a href="https://allegro.pl/moje-allegro/sprzedaz/zamowienia/${msg.allegro_order_id}" target="_blank" class="btn allegro-msg-order-id">Przejdź do zamówienia</a></div>`
                     : `` }
                    ${attachmentsTemplate}
                    <div class="allegro-msg-id">Id wiadomości na Allegro: ${msg.allegro_msg_id}</div>
                </div>
            </div>
        `;
    }
    
    async closeChat() {

        const url = this.ajaxPath + 'allegro/exitChat/'+this.threadId;
        await ajaxPost({}, url);

        window.localStorage.removeItem('allegro_chat_last_check');
        window.localStorage.removeItem('DataTables_dataTable_/admin/orders');
        this.isChatWindowOpen = false;
        
        window.close();
    }

    inactiveCountdown() {
        clearTimeout(this.timeout);

        // restart timeout
        // auto close window after 15 min.
        this.timeout = setTimeout(() => {
            this.closeChat();
        }, 900000);
    }

    chatScrollDown() {
        setTimeout(() => {
            const msgsWrapper = $('.allegro-msgs-wrapper');
            msgsWrapper.trigger('focus');
            msgsWrapper.scrollTop(msgsWrapper[0].scrollHeight);
        }, 150);
    }

    async getNewMessages() {
        const lastDate = $('.allegro-msg-wrapper:last-child .allegro-msg-date').data('date');
        if(!lastDate) return false;

        const data = {
            threadId: this.threadId,
            lastDate,
            isPreview: this.isPreview,
        }
        const url = this.ajaxPath + 'allegro/getNewMessages/'+this.threadId;
        let messages = await ajaxPost(data, url);
        this.footer.removeClass('loader-2');
        
        if(messages == 'null') return false;

        this.inactiveCountdown();

        const messagesTemplate = messages.map(msg => this.makeSingleMessageTemplate(msg)).join('');

        $('.allegro-msgs-wrapper').append(messagesTemplate);
        this.chatScrollDown();
    }

    async sendMessage() {
        const textarea = $('.allegro-textarea');
        const attachmentInput = $('.allegro-add-attachment')[0];
        const content = textarea.val();
        let attachmentId = null;

        if(attachmentInput.files.length > 0) {
            const file = attachmentInput.files[0];
            const filename = file.name;
            
            const data = {
                filename,
                size: file.size,
            };
            let url = this.ajaxPath + 'allegro/newAttachmentDeclaration';
            attachmentId = await ajaxPost(data, url);

            if(attachmentId == 'null') {
                toastr.error('Nie udało się wysłać wiadomości.');
                return false;
            }

            const formData = new FormData();
            formData.append('file', file);
            
            url = this.ajaxPath + 'allegro/uploadAttachment/' + attachmentId;
            attachmentId = await ajaxFormData(formData, url);
            
            if(attachmentId == 'null') {
                toastr.error('Nie udało się wysłać wiadomości.');
                return false;
            }
        }
        
        const data = {
            threadId: this.threadId,
            content,
            attachmentId,
        }

        const url = this.ajaxPath + 'allegro/writeNewMessage';
        let res = await ajaxPost(data, url);

        if(res != 'OK') {
            toastr.error('Nie udało się wysłać wiadomości.');
            return false;
        }

        textarea.val('');
        $('.allegro-add-attachment').val('');
        
        this.inactiveCountdown();

    }

    renderChat(messages, nickname) {

        const messagesTemplate = messages.map(msg => this.makeSingleMessageTemplate(msg)).join('');

        const chatTemplate = `
        <div class="allegro-chat-wrapper ${this.isPreview ? 'preview' : ''}">
            <h2>Czat Allegro</h2>
            <h3>Dotyczy użytkownika Allegro: ${nickname}</h3>
            <h3>ID czatu na Allegro: ${this.threadId}</h3>
            <div class="allegro-msgs-wrapper">${messagesTemplate}</div>
            ${!this.isPreview ? `<hr>
            <div class="allegro-chat-footer">
                <textarea class="allegro-textarea"></textarea>
                <div>
                    <input class="allegro-add-attachment" type="file" value="Dodaj załącznik" />
                </div>
                <div class="btn allegro-send">Wyślij wiadomość</div>
                <div class="btn allegro-close-conversation">Zamknij konwersację</div>
            </div>` : ``}
        </div>`;

        document.body.innerHTML = chatTemplate;
    }
}
