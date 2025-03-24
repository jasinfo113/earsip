function showDataTicket(str) {
    if (str === 'response') {
        dataTicketResponse();
    }
}

function dataTicketResponse(_page = 0) {
    listPagination(_page, 'main/ticket/response/data', '#form_ticket_response', '#form_ticket_response #list_data');
}