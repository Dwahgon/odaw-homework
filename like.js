function onLike(button) {
    const counter = document.getElementById('like-counter');
    const liked = button.classList.contains('material-icons-outlined');

    counter.innerText = Number.parseInt(counter.innerText) + (liked ? 1 : -1);
    button.classList.remove(liked ? 'material-icons-outlined' : 'material-icons');
    button.classList.add(liked ? 'material-icons' : 'material-icons-outlined');
}

document.addEventListener('DOMContentLoaded', () => {
    const counter = document.getElementById('like-counter')
    counter.innerText = Math.floor(Math.random() * 100)
})