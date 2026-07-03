(function () {
  var friendsSelect = document.getElementById('members-friends');
  var friendsList = document.getElementById('members-friends-list');

  if (!friendsSelect || !friendsList || !window.UteHubMemberSingle) {
    return;
  }

  function setCookie(name, value) {
    document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; SameSite=Lax';
  }

  function currentPageFromClick(link) {
    var current = friendsList.querySelector('.pagination-links .current');
    var page = current ? parseInt(current.textContent.replace(/\D/g, ''), 10) : 1;

    if (link.classList.contains('next')) {
      return page + 1;
    }

    if (link.classList.contains('prev')) {
      return Math.max(1, page - 1);
    }

    return parseInt(link.textContent.replace(/\D/g, ''), 10) || 1;
  }

  function refreshFriends(orderBy, page) {
    var body = new URLSearchParams();

    setCookie('bp-members-scope', 'friends');
    setCookie('bp-members-filter', orderBy);

    body.set('action', 'members_filter');
    body.set('object', 'members');
    body.set('filter', orderBy);
    body.set('scope', 'friends');
    body.set('page', String(page || 1));
    body.set('user_id', friendsList.getAttribute('data-user-id') || '');
    body.set('cookie', document.cookie);

    friendsList.classList.add('loading');

    fetch(window.UteHubMemberSingle.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: body.toString()
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Friends request failed.');
        }

        return response.text();
      })
      .then(function (html) {
        friendsList.innerHTML = html;
      })
      .catch(function () {
        window.location.reload();
      })
      .finally(function () {
        friendsList.classList.remove('loading');
      });
  }

  friendsSelect.addEventListener('change', function (event) {
    event.preventDefault();
    event.stopImmediatePropagation();
    refreshFriends(friendsSelect.value, 1);
  }, true);

  friendsList.addEventListener('click', function (event) {
    var link = event.target.closest('.pagination-links a');

    if (!link) {
      return;
    }

    event.preventDefault();
    event.stopImmediatePropagation();
    refreshFriends(friendsSelect.value, currentPageFromClick(link));
  });
}());
