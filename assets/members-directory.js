(function () {
  var select = document.getElementById('members-order-by');
  var list = document.getElementById('members-dir-list');
  var pendingRequest = null;

  if (!select || !list || !window.UteHubMembersDirectory) {
    return;
  }

  function debounce(fn, delay) {
    var timeout;
    return function () {
      var args = arguments;
      var context = this;
      clearTimeout(timeout);
      timeout = setTimeout(function () {
        fn.apply(context, args);
      }, delay);
    };
  }

  function getSelectedScope() {
    var selected = document.querySelector('.members-tabs li.selected');

    if (!selected || !selected.id) {
      return 'all';
    }

    return selected.id.replace(/^members-/, '') || 'all';
  }

  function getSearchTerms() {
    var search = document.getElementById('members_search');

    return search ? search.value : '';
  }

  function setCookie(name, value) {
    document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; SameSite=Lax';
  }

  function refreshMembers(orderBy) {
    var scope = getSelectedScope();
    var body = new URLSearchParams();

    setCookie('bp-members-scope', scope);
    setCookie('bp-members-filter', orderBy);

    body.set('action', 'members_filter');
    body.set('object', 'members');
    body.set('filter', orderBy);
    body.set('scope', scope);
    body.set('page', '1');
    body.set('search_terms', getSearchTerms());
    body.set('cookie', document.cookie);

    list.classList.add('loading');

    fetch(window.UteHubMembersDirectory.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: body.toString()
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Members request failed.');
        }

        return response.text();
      })
      .then(function (html) {
        list.innerHTML = html;
      })
      .catch(function () {
        window.location.href = window.UteHubMembersDirectory.membersUrl + '?members_search=' + encodeURIComponent(getSearchTerms());
      })
      .finally(function () {
        list.classList.remove('loading');
      });
  }

  var debouncedRefresh = debounce(function (orderBy) {
    refreshMembers(orderBy);
  }, 300);

  document.addEventListener('change', function (event) {
    if (event.target !== select) {
      return;
    }

    debouncedRefresh(select.value);
  });
}());
