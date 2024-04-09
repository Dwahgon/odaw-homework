function addComment(form) {
    const commentBody = form['comment-textarea'].value;
    if (!commentBody.length) {
        alert("Não é possível publicar comentários vazios");
        return false;
    }

    const commentsList = document.getElementById('comments-list');

    const commentLi = document.createElement('li');
    commentLi.classList.add("list-group-item");
    const commentId = `comment-${new Date().getTime()}`;
    commentLi.id = commentId;

    const commentAuthor = document.createElement('h3');
    commentAuthor.innerHTML = "<i>Usuário: Usuário123</i>"

    const commentP = document.createElement('p');
    commentP.innerText = form['comment-textarea'].value;

    commentLi.appendChild(commentAuthor);
    commentLi.appendChild(commentP);
    commentsList.appendChild(commentLi);

    location.href = '#' + commentId;

    form['comment-textarea'].value = ''

    return false;
}