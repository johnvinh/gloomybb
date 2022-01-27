function getTopicId()
{
    const regex = new RegExp('^.+?\\?id=(.+)$');
    return regex.exec(window.location.href)[1];
}

document.querySelector('#new-reply').addEventListener('click', (e) =>
{
    window.location.href = `newreply.php?id=${getTopicId()}`;
});