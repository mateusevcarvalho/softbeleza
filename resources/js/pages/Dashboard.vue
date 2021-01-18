<template>
  <page-component title="" :breadcrumb="[]">
    <div class="row">
      <div class="col-sm-12">
        <card-component :load="load">

          <div class="mb-5 text-center">
            <h3>Seja bem vindo(a) ao Softbeleza!</h3>
            Você está a poucos passos de uma rotina muito mais organizada! Basta seguir o passo-a-passo abaixo.
          </div>

          <ul class="list-group">
            <li class="list-group-item">
              <h5><i class="fas fa-clock"></i> 1. Defina o horário de funcionamento</h5>
              <p style="margin-left: 33px" class="text-muted">
                Configure os horários de funcionamento do seu estabelecimento.
                <span class="float-right">
                <a href="/configuracoes/estabelecimento" class="btn btn-info">
                  Definir Horários
                </a>
              </span>
              </p>
            </li>
          </ul>

          <ul class="list-group mt-3">
            <li class="list-group-item">
              <h5><i class="fas fa-money-check-alt"></i> 2. Cadastre seus meios de pagamento</h5>
              <p style="margin-left: 33px" class="text-muted">
                Cadastre os meios de pagamentos que serão aceitos no seu estabelecimento.
                <span class="float-right">
                <a href="/meios-pagamentos" class="btn btn-info">
                  Cadastrar Meios de Pagamentos
                </a>
              </span>
              </p>
            </li>
          </ul>

          <ul class="list-group mt-3">
            <li class="list-group-item">
              <h5><i class="fas fa-cut"></i> 3. Cadastre seus serviços prestados</h5>
              <p style="margin-left: 33px" class="text-muted">
                Cadastre os serviços que cada profissional do seu estabelecimento realiza.
                <span class="float-right">
                <a href="/servicos" class="btn btn-info">
                  Cadastrar Serviços
                </a>
              </span>
              </p>
            </li>
          </ul>

          <ul class="list-group mt-3">
            <li class="list-group-item">
              <h5><i class="fas fa-id-card-alt"></i> 4. Defina os serviços prestados ao profissional</h5>
              <p style="margin-left: 33px" class="text-muted">
                Cadastre e defina serviços prestados por cada profissional.
                <span class="float-right">
                <a href="/profissionais" class="btn btn-info">
                  Definir Profissionais
                </a>
              </span>
              </p>
            </li>
          </ul>

        </card-component>
      </div>
    </div>
  </page-component>
</template>

<script>
export default {
  name: "Dashboard",
  data() {
    return {
      load: false
    }
  }
}
</script>

<style scoped>

</style>
