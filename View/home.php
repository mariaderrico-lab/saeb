<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SAEPVision</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  
  <header class="cabecalho">
   
    <section class="secao-usuario">
      <img id="usuarioFoto" class="foto-perfil" src="assets/usuarios/saepVision.jpeg" alt="Foto de perfil">
      <span id="usuarioNome" class="usuario-nome">@SAEPVision</span>
      <button id="btnVerPerfil" class="btn btn-preto desabilitado" type="button">Ver Perfil</button>
      <button id="btnLogout" class="btn btn-preto" type="button" hidden>Logout</button>
    </section>
    
    <section class="secao-empresa">SAEPVision</section>

    <section class="secao-login">
      <button id="btnLogin" class="btn btn-preto" type="button">Login</button>
    </section>
  </header>

  <main class="corpo">
  
    <div class="busca-bloco">
      <div class="campo-busca">
        <input id="campoBusca" type="text" placeholder="Digite o nome do fotógrafo... ex: @fotografo1" autocomplete="off">
        <button id="btnBusca" type="button" aria-label="Pesquisar"><img src="assets/icones/lupa.svg" alt=""></button>
      </div>
      <p id="erroBusca" class="erro" role="alert"></p>
    </div>

    <section id="galeria" class="galeria" aria-live="polite"></section>
  </main>

  <footer class="rodape">
    <span class="rodape-empresa">SAEPVision</span>
    <div class="rodape-redes">
      <img src="assets/icones/instagram.svg" alt="Instagram">
      <img src="assets/icones/tiktok.svg" alt="TikTok">
      <img src="assets/icones/linkedIn.svg" alt="LinkedIn">
      <img src="assets/icones/youtube.svg" alt="YouTube">
    </div>
    <span class="rodape-copy">Copyright 2026-2027</span>
  </footer>

  <div id="modalLogin" class="overlay" hidden>
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="tituloLogin">
      <button id="fecharLogin" class="fechar" type="button" aria-label="Fechar"><img src="assets/icones/close.svg" alt=""></button>
      <h2 id="tituloLogin">Login</h2>
      <form id="formLogin" novalidate>
        <label class="campo">
          <input id="email" type="text" placeholder="Digite seu e-mail" autocomplete="username">
          <span id="erroEmail" class="erro"></span>
        </label>
        <label class="campo">
          <input id="senha" type="password" placeholder="Digite sua senha" autocomplete="current-password">
          <span id="erroSenha" class="erro"></span>
        </label>
        <p id="erroLogin" class="erro erro-geral" role="alert"></p>
        <div class="acoes">
          <button id="btnCancelar" class="btn btn-vazado" type="button">Cancelar</button>
          <button class="btn btn-preto" type="submit">Login</button>
        </div>
      </form>
    </div>
  </div>

  <div id="overlayMenu" class="overlay overlay-lateral" hidden>
    <aside class="menu-lateral" role="dialog" aria-modal="true" aria-label="Perfil">
      <button id="fecharMenu" class="fechar" type="button" aria-label="Fechar"><img src="assets/icones/close.svg" alt=""></button>
      <img id="menuFoto" class="foto-perfil foto-grande" src="" alt="">
      <h3 id="menuNome"></h3>
      <p class="menu-info"><strong id="menuLikes">0</strong> Likes</p>
      <p class="menu-info"><strong id="menuPubs">0</strong> Publicações</p>

      <button id="btnSuasPub" class="btn btn-destaque" type="button">Suas publicações</button>
      <div id="listaMinhas" class="lista-minhas"></div>

      <form id="formNovaPub" class="nova-pub" novalidate>
        <h4>Nova publicação</h4>
        <input id="pubTitulo" type="text" placeholder="Título (ex: Foto 10)">
        <input id="pubLocal" type="text" placeholder="Local (ex: Rio de Janeiro)">
        <input id="pubArquivo" type="file" accept="image/jpeg,image/png,image/webp">
        <p id="erroPub" class="erro"></p>
        <button class="btn btn-preto" type="submit">Publicar</button>
      </form>
    </aside>
  </div>

  <script src="js/app.js"></script>
</body>
</html>
