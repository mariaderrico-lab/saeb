'use strict';

const $ = (id) => document.getElementById(id);
let usuario = null;          
let fotografoFiltro = null; 

async function api(url, opcoes = {}) {
  const r = await fetch(url, opcoes);
  const dados = await r.json().catch(() => ({ erro: 'Resposta inválida do servidor.' }));
  return { ok: r.ok, status: r.status, dados };
}
const postJSON = (url, corpo) =>
  api(url, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(corpo) });

const escapar = (t) => String(t).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

async function carregarSessao() {
  const { dados } = await api('index.php?url=auth/sessao');
  usuario = dados.usuario;
  atualizarCabecalho();
}

function atualizarCabecalho() {
  const logado = !!usuario;
  $('usuarioFoto').src = logado ? usuario.imagem : 'assets/usuarios/saepVision.jpeg';
  $('usuarioNome').textContent = logado ? '@' + usuario.nome : '@SAEPVision';
  $('btnLogin').hidden = logado;
  $('btnLogout').hidden = !logado;

  const habilitado = logado && usuario.tipo === 'fotografo';
  $('btnVerPerfil').classList.toggle('desabilitado', !habilitado);
}

function cardHTML(p) {
  return `
    <article class="card" data-id="${p.id_publicacao}">
      <img class="foto" src="${escapar(p.url_fotografia)}" alt="${escapar(p.titulo_imagem)}">
      <span class="tooltip">Foto tirada por: @${escapar(p.fotografo)}</span>
      <div class="info">
        <div class="titulo">${escapar(p.titulo_imagem)}</div>
        <button class="curtir ${p.curtiu ? 'ativo' : ''}" type="button" data-id="${p.id_publicacao}" aria-label="Curtir">
          <span class="coracao"></span><span class="contador">${p.curtidas}</span>
        </button>
        <div class="local">Local: ${escapar(p.local_foto)}</div>
      </div>
    </article>`;
}

function urlGaleria() {
  return fotografoFiltro ? `index.php?url=publicacao/listar&fotografo=${fotografoFiltro}` : 'index.php?url=publicacao/listar';
}

async function carregarGaleria() {
  const { dados } = await api(urlGaleria());
  const lista = dados.publicacoes || [];
  $('galeria').innerHTML = lista.length ? lista.map(cardHTML).join('') : '<p class="vazio">Nenhuma publicação encontrada.</p>';
}

async function atualizarCurtidas() {
  const { dados } = await api(urlGaleria());
  (dados.publicacoes || []).forEach((p) => {
    const card = document.querySelector(`.card[data-id="${p.id_publicacao}"]`);
    if (!card) return;
    card.querySelector('.contador').textContent = p.curtidas;
    card.querySelector('.curtir').classList.toggle('ativo', p.curtiu);
  });
}

$('galeria').addEventListener('click', async (e) => {
  const btn = e.target.closest('.curtir');
  if (!btn) return;
  if (!usuario) return abrirLogin();
  const { ok, dados } = await postJSON('index.php?url=publicacao/curtir', { id_publicacao: Number(btn.dataset.id) });
  if (ok) {
    btn.classList.toggle('ativo', dados.curtiu);
    btn.querySelector('.contador').textContent = dados.curtidas;
    if (usuario.tipo === 'fotografo') atualizarTotaisMenu();
  }
});

setInterval(() => { if (!document.hidden) atualizarCurtidas(); }, 4000);

async function buscar() {
  if (!usuario) return abrirLogin();
  const termo = $('campoBusca').value.trim().replace(/^@/, '');
  $('erroBusca').textContent = '';

  if (termo === '') { 
    fotografoFiltro = null;
    return carregarGaleria();
  }
  if (!/^[A-Za-z0-9_]+$/.test(termo)) {
    $('erroBusca').textContent = 'Formato inválido (exemplo: fotografo1).';
    return;
  }
  const { ok, dados } = await api('index.php?url=publicacao/buscar&q=' + encodeURIComponent(termo));
  if (!ok) { $('erroBusca').textContent = dados.erro; return; }
  fotografoFiltro = dados.publicacoes.length ? dados.publicacoes[0].id_usuario : null;
  $('galeria').innerHTML = dados.publicacoes.length ? dados.publicacoes.map(cardHTML).join('') : '<p class="vazio">Nenhuma publicação encontrada.</p>';
}

$('btnBusca').addEventListener('click', buscar);
$('campoBusca').addEventListener('keydown', (e) => { if (e.key === 'Enter') buscar(); });

$('campoBusca').addEventListener('focus', () => { if (!usuario) { $('campoBusca').blur(); abrirLogin(); } });

function abrirLogin() {
  $('formLogin').reset();
  ['erroEmail', 'erroSenha', 'erroLogin'].forEach((id) => ($(id).textContent = ''));
  $('modalLogin').hidden = false;
  $('email').focus();
}
const fecharLogin = () => ($('modalLogin').hidden = true);

$('btnLogin').addEventListener('click', abrirLogin);
$('fecharLogin').addEventListener('click', fecharLogin);
$('btnCancelar').addEventListener('click', fecharLogin);

$('formLogin').addEventListener('submit', async (e) => {
  e.preventDefault();
  const email = $('email').value.trim();
  const senha = $('senha').value;
  $('erroEmail').textContent = '';
  $('erroSenha').textContent = '';
  $('erroLogin').textContent = '';

  let valido = true;
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { $('erroEmail').textContent = 'E-mail inválido ou vazio.'; valido = false; }
  if (senha === '') { $('erroSenha').textContent = 'A senha é obrigatória e não pode estar vazia.'; valido = false; }
  if (!valido) return;

  const { ok, dados } = await postJSON('index.php?url=auth/login', { email, senha });
  if (!ok) { $('erroLogin').textContent = dados.erro; return; }

  usuario = dados.usuario;
  fecharLogin();          
  atualizarCabecalho();   
  carregarGaleria();       
});

$('btnLogout').addEventListener('click', async () => {
  await api('index.php?url=auth/logout');
  window.location.href = 'index.php'; 
});

async function atualizarTotaisMenu() {
  const { dados } = await api('index.php?url=auth/sessao');
  if (!dados.usuario) return;
  $('menuLikes').textContent = dados.usuario.total_likes;
  $('menuPubs').textContent = dados.usuario.total_publicacoes;
}

$('btnVerPerfil').addEventListener('click', async () => {
  if (!usuario) return abrirLogin();                 
  if (usuario.tipo !== 'fotografo') return;          
  $('menuFoto').src = usuario.imagem;
  $('menuNome').textContent = '@' + usuario.nome;
  $('listaMinhas').innerHTML = '';
  $('overlayMenu').hidden = false;
  atualizarTotaisMenu();
});

$('fecharMenu').addEventListener('click', () => ($('overlayMenu').hidden = true));
$('overlayMenu').addEventListener('click', (e) => { if (e.target === $('overlayMenu')) $('overlayMenu').hidden = true; });

async function carregarMinhas() {
  const { dados } = await api('index.php?url=publicacao/minhas');
  const lista = dados.publicacoes || [];
  $('listaMinhas').innerHTML = lista.length
    ? lista.map((p) => `
        <div class="mini">
          <img class="foto" src="${escapar(p.url_fotografia)}" alt="">
          <div><strong>${escapar(p.titulo_imagem)}</strong><br><small>${p.curtidas} curtidas · ${escapar(p.local_foto)}</small></div>
          <button class="excluir" type="button" data-id="${p.id_publicacao}" aria-label="Excluir"><img src="assets/icones/lixeira.svg" alt=""></button>
        </div>`).join('')
    : '<p>Você ainda não publicou fotos.</p>';
}

$('btnSuasPub').addEventListener('click', carregarMinhas);

$('listaMinhas').addEventListener('click', async (e) => {
  const btn = e.target.closest('.excluir');
  if (!btn || !confirm('Excluir esta publicação?')) return;
  const { ok } = await postJSON('index.php?url=publicacao/excluir', { id_publicacao: Number(btn.dataset.id) });
  if (ok) { carregarMinhas(); carregarGaleria(); atualizarTotaisMenu(); }
});

$('formNovaPub').addEventListener('submit', async (e) => {
  e.preventDefault();
  $('erroPub').textContent = '';
  const fd = new FormData();
  fd.append('titulo', $('pubTitulo').value);
  fd.append('local', $('pubLocal').value);
  if ($('pubArquivo').files[0]) fd.append('foto', $('pubArquivo').files[0]);
  const { ok, dados } = await api('index.php?url=publicacao/criar', { method: 'POST', body: fd });
  if (!ok) { $('erroPub').textContent = dados.erro; return; }
  $('formNovaPub').reset();
  carregarMinhas(); carregarGaleria(); atualizarTotaisMenu();
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') { fecharLogin(); $('overlayMenu').hidden = true; }
});

(async function iniciar() {
  await carregarSessao();
  await carregarGaleria();
})();
