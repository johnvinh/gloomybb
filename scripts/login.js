document.querySelector('input[name=login]').addEventListener('click', (e) =>
{
    const username = document.querySelector('#username').value;
    const password = document.querySelector('#password').value;
    if (username === '') {
        e.preventDefault();
        alert("Please enter a username.");
        return;
    } else if (password === '') {
        e.preventDefault();
        alert("Please enter a password.");
        return;
    }
    e.target.parentElement.submit();
});