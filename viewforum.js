function getForumId() {
    const regex = new RegExp("^.+?\\?id=(.+)$");
    return regex.exec(window.location.href)[1];
}

document.querySelector("#new-topic").addEventListener("click", (e) => {
    window.location.href = `newtopic.php?forum_id=${getForumId()}`;
});