document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  const resposta = document.getElementById('resposta');
  if (!form || !resposta) return;

  const botao = form.querySelector('button[type="submit"]');

  form.addEventListener('submit', async (evento) => {
    evento.preventDefault();       
    botao.disabled = true;
    resposta.className = '';
    resposta.textContent = 'Enviando...';

    try {
      const r = await fetch(form.action, { method: 'POST', body: new FormData(form) });
      const dados = await r.json();  
      mostrarResposta(dados);
      if (dados.sucesso) form.reset();
    } catch (erro) {
      mostrarResposta({ sucesso: false, mensagem: 'Falha na comunicação com o servidor. O PHP está rodando?' });
    } finally {
      botao.disabled = false;
    }
  });

  function mostrarResposta(dados) {
    resposta.className = dados.sucesso ? 'ok' : 'erro';
    resposta.textContent = '';

    const p = document.createElement('p');
    p.style.margin = '0';
    p.textContent = dados.mensagem;
    resposta.appendChild(p);

    if (Array.isArray(dados.erros) && dados.erros.length) {
      const ul = document.createElement('ul');
      dados.erros.forEach((texto) => {
        const li = document.createElement('li');
        li.textContent = texto;
        ul.appendChild(li);
      });
      resposta.appendChild(ul);
    }
  }
});
