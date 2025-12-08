(function(){
  const yearEl = document.getElementById('year');
  if (yearEl) { yearEl.textContent = new Date().getFullYear(); }

  const quoteRefEl = document.getElementById('quoteRef');
  const payfastBtn = document.getElementById('payfastBtn');
  const ozowBtn = document.getElementById('ozowBtn');

  // Backend base URL: set window.BCS_BACKEND_BASE_URL in a script tag if you deploy elsewhere
  const BACKEND_BASE_URL = window.BCS_BACKEND_BASE_URL || 'http://localhost:8000';

  async function getPaymentUrl(provider, reference) {
    const endpoint = provider === 'payfast' ? '/api/payments/create/payfast' : '/api/payments/create/ozow';
    const successUrl = new URL('success.html', window.location.href).toString();
    const cancelUrl = new URL('cancel.html', window.location.href).toString();

    const res = await fetch(BACKEND_BASE_URL + endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reference, success_url: successUrl, cancel_url: cancelUrl })
    });

    const text = await res.text();
    if (!res.ok) {
      // Surface server response for easier debugging
      throw new Error('HTTP ' + res.status + ': ' + text);
    }

    let data = {};
    try {
      data = text ? JSON.parse(text) : {};
    } catch (e) {
      throw new Error('Invalid JSON from server: ' + text);
    }

    if (!data.url) throw new Error('No URL returned');
    return data.url;
  }

  function handleClick(provider) {
    if (!quoteRefEl) return;
    const ref = (quoteRefEl.value || '').trim();
    if (!ref) { alert('Please enter your Quote/Reference.'); return; }

    getPaymentUrl(provider, ref)
      .then(url => {
        // Prefix relative URLs with backend base
        if (!/^https?:\/\//i.test(url)) {
          const base = BACKEND_BASE_URL.replace(/\/$/, '');
          if (!url.startsWith('/')) url = '/' + url;
          url = base + url;
        }
        window.location.href = url;
      })
      .catch(err => {
        console.error(err);
        alert('Unable to start payment. ' + (err && err.message ? err.message : 'Please try again.'));
      });
  }

  if (payfastBtn) payfastBtn.addEventListener('click', () => handleClick('payfast'));
  if (ozowBtn) ozowBtn.addEventListener('click', () => handleClick('ozow'));
})();