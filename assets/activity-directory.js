(function () {
  var shell = document.querySelector('.activity-directory-shell');
  var list = document.querySelector('.activity-directory-shell [data-bp-list="activity"]');
  var filter = document.getElementById('activity-filter-by');
  var searchForm = document.getElementById('search-activity-form');
  var config = window.UteHubActivityDirectory || {};

  if (!shell || !list || !filter) {
    return;
  }

  config.ajaxUrl = config.ajaxUrl || shell.getAttribute('data-ajax-url') || window.location.origin + '/wp-admin/admin-ajax.php';
  config.activityUrl = config.activityUrl || shell.getAttribute('data-activity-url') || window.location.href.split('#')[0];

  function getSelectedScope() {
    var selected = shell.querySelector('.activity-type-tabs li.selected');

    if (!selected || !selected.id) {
      return 'all';
    }

    return selected.id.replace(/^activity-/, '') || 'all';
  }

  function getSearchTerms() {
    var search = document.getElementById('activity_search');

    return search ? search.value : '';
  }

  function setCookie(name, value) {
    document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; SameSite=Lax';
  }

  function setSelectedScope(scope) {
    shell.querySelectorAll('.activity-type-tabs li').forEach(function (item) {
      item.classList.toggle('selected', item.id === 'activity-' + scope);
    });
  }

  function refreshActivity(scope, activityFilter) {
    var body = new URLSearchParams();

    scope = scope || getSelectedScope();
    activityFilter = activityFilter || filter.value;

    setCookie('bp-activity-scope', scope);
    setCookie('bp-activity-filter', activityFilter);

    body.set('action', 'activity_widget_filter');
    body.set('scope', scope);
    body.set('filter', activityFilter);
    body.set('search_terms', getSearchTerms());
    body.set('cookie', document.cookie);

    list.classList.add('loading');

    return fetch(config.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
      },
      body: body.toString()
    })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Activity request failed.');
        }

        return response.json();
      })
      .then(function (response) {
        if (!response || typeof response.contents === 'undefined') {
          throw new Error('Activity response was empty.');
        }

        list.innerHTML = response.contents;
        setSelectedScope(scope);
      })
      .catch(function () {
        window.location.href = config.activityUrl;
      })
      .finally(function () {
        list.classList.remove('loading');
      });
  }

  shell.addEventListener('click', function (event) {
    var link = event.target.closest('.activity-type-tabs a');
    var item = link ? link.closest('li[id^="activity-"]') : null;

    if (!item) {
      return;
    }

    event.preventDefault();
    event.stopImmediatePropagation();

    refreshActivity(item.id.replace(/^activity-/, ''), filter.value);
  }, true);

  filter.addEventListener('change', function (event) {
    event.preventDefault();
    event.stopImmediatePropagation();

    refreshActivity(getSelectedScope(), filter.value);
  }, true);

  if (searchForm) {
    searchForm.addEventListener('submit', function (event) {
      event.preventDefault();
      event.stopImmediatePropagation();

      refreshActivity(getSelectedScope(), filter.value);
    }, true);
  }
}());
