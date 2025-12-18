const modification = (() => {
  async function load(id, form, statusEl) {
    try {
      statusEl.textContent = 'Chargement...';
      statusEl.style.color = '#666';
      const res = await fetch(`/courses/${id}`);
      const j = await res.json().catch(()=>null);
      if (!res.ok) throw new Error((j && (j.message || j.error)) ? (j.message || j.error) : `${res.status} ${res.statusText}`);
      if (!j || j.success !== true || !j.data) throw new Error('Données introuvables');
      const c = j.data;
      form.elements['date_course'].value = c.date_course || '';
      form.elements['heure_debut'].value = c.heure_debut || '';
      form.elements['heure_fin'].value = c.heure_fin || '';
      form.elements['km'].value = c.km ?? '';
      form.elements['montant'].value = c.montant ?? '';
      form.elements['depart'].value = c.depart || '';
      form.elements['arrivee'].value = c.arrivee || '';
      form.elements['conducteur_id'].value = c.conducteur_id ?? '';
      form.elements['moto_id'].value = c.moto_id ?? '';
      form.elements['client_id'].value = c.client_id ?? '';
      if (Number(c.valide) === 1) {
        statusEl.textContent = '⚠️ Cette course est déjà validée — modification interdite.';
        statusEl.style.color = '#dc2626';
        form.style.display = 'none';
      } else {
        statusEl.textContent = '';
        form.style.display = 'block';
      }
    } catch (err) {
      statusEl.textContent = '❌ Erreur: ' + err.message;
      statusEl.style.color = '#dc2626';
      form.style.display = 'none';
    }
  }

  function bindSubmit(id, form) {
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const formData = new FormData(this);
      const data = Object.fromEntries(formData.entries());
      Object.keys(data).forEach(key => { if (data[key] === '') data[key] = null; });
      try {
        const res = await fetch(`/courses/update/${id}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams(data)
        });
        const result = await res.json().catch(()=>null);
        if (!res.ok || !result || result.success !== true) {
          const msg = result && (result.message || result.error) ? (result.message || result.error) : `${res.status} ${res.statusText}`;
          throw new Error(msg || 'Erreur de mise à jour');
        }
        alert('✅ Course modifiée avec succès !');
        window.location.href = '/ui/courses';
      } catch (err) {
        alert('❌ Erreur: ' + err.message);
      }
    });
  }

  return { load, bindSubmit };
})();