function toggleLike(postId, event) {
    event.preventDefault(); 

    fetch('toggle_like.php?id=' + postId, {
        method: 'GET'
    }).then(response => response.json())
        .then(data => {
            if (data.success) {
                const likeButton = document.querySelector(`.icon-btn[data-post-id="${postId}"]`);
                const likeCount = likeButton.nextElementSibling;


                if (data.liked) {
                    likeButton.classList.remove('like-btn');
                    likeButton.classList.add('liked-btn');
                } else {
                    likeButton.classList.remove('liked-btn');
                    likeButton.classList.add('like-btn');
                }

                likeCount.textContent = data.newLikeCount;
            } else {
                console.error('Failed to toggle like.');
            }
        }).catch(error => {
            console.error('Error:', error);
        });
}
