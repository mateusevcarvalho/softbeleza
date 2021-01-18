import Dashboard from "../pages/Dashboard";
import Login from "../pages/autenticacao/Login";
import Registro from "../pages/autenticacao/Registro";
import Confirmacao from "../pages/autenticacao/Confirmacao";
import Estabelecimento from "../pages/configuracoes/Estabelecimento";
import Servicos from "../pages/configuracoes/Servicos";
import Master from "../layouts/Master";
import Auth from "../layouts/Auth";
import Profissionais from "../pages/profissionais/Profissionais";
import MeiosPagamentos from "../pages/configuracoes/MeiosPagamentos";
import Clientes from "../pages/clientes/Clientes";
import Produtos from "../pages/produtos/Produtos";
import Fornecedores from "../pages/fornecedores/Fornecedores";
import LancarMovimentacao from "../pages/produtos/LancarMovimentacao";
import ExtratoProduto from "../pages/produtos/ExtratoProduto";
import Agenda from "../pages/Agenda/Agenda";
import Caixa from "../pages/caixa/Caixa";
import FechamentoCaixa from "../pages/relatorios/fechamento-caixa/FechamentoCaixa";
import Detalhes from "../pages/relatorios/fechamento-caixa/Detalhes";
import DetalhesRateio from "../pages/relatorios/rateio-profissional/Detalhes";
import FaturamentoPeriodo from "../pages/relatorios/faturamento-periodo/FaturamentoPeriodo";
import FaturamentoFormaPagamento from "../pages/relatorios/faturamento-forma-pagamento/FaturamentoFormaPagamento";
import RateioProfissional from "../pages/relatorios/rateio-profissional/RateioProfissional";
import Aniversariantes from "../pages/relatorios/aniversariantes/Aniversariantes";
import MeusDados from "../pages/meus-dados/MeusDados";
import RecuperarSenha from "../pages/autenticacao/RecuperarSenha";
import NovaSenha from "../pages/autenticacao/NovaSenha";

const routes = [
    {
        name: 'auth', path: '/', component: Auth, children: [
            {name: 'login', path: '', component: Login, meta: {requireAuth: false}},
            {name: 'registro', path: '/cadastro', component: Registro, meta: {requireAuth: false}},
            {name: 'confirmacao', path: '/confirmacao/:id', component: Confirmacao, meta: {requireAuth: false}},
            {name: 'recuperar-senha', path: '/recuperar-senha', component: RecuperarSenha, meta: {requireAuth: false}},
            {name: 'recuperar-senha-id', path: '/nova-senha/:id', component: NovaSenha, meta: {requireAuth: false}},
        ]
    },

    {
        name: 'master', path: '/', component: Master, children: [
            {name: 'tour', path: '/tour', component: Dashboard, meta: {requireAuth: true}},
            {
                name: 'estabelecimento',
                path: '/configuracoes/estabelecimento',
                component: Estabelecimento,
                meta: {requireAuth: true, permission: 'configuracao'}
            },
            {
                name: 'servicos',
                path: '/servicos',
                component: Servicos,
                meta: {requireAuth: true, permission: 'servicos'}
            },
            {
                name: 'profissionais',
                path: '/profissionais',
                component: Profissionais,
                meta: {requireAuth: true, permission: 'profissionais'}
            },
            {
                name: 'meiosPagamentos',
                path: '/meios-pagamentos',
                component: MeiosPagamentos,
                meta: {requireAuth: true, permission: 'configuracao'}
            },
            {
                name: 'clientes',
                path: '/clientes',
                component: Clientes,
                meta: {requireAuth: true, permission: 'clientes'}
            },
            {name: 'estoque', path: '/estoque', component: Produtos, meta: {requireAuth: true, permission: 'estoque'}},
            {
                name: 'extratoProduto',
                path: '/estoque/extrato-produto/:id',
                component: ExtratoProduto,
                meta: {requireAuth: true, permission: 'estoque'}
            },
            {
                name: 'lancarMovimentacao',
                path: '/estoque/lancar-movimentacao',
                component: LancarMovimentacao,
                meta: {requireAuth: true, permission: 'estoque'}
            },
            {
                name: 'fornecedores',
                path: '/fornecedores',
                component: Fornecedores,
                meta: {requireAuth: true, permission: 'fornecedores'}
            },
            {name: 'agenda', path: '/agenda', component: Agenda, meta: {requireAuth: true, permission: 'agenda'}},
            {name: 'caixa', path: '/caixa', component: Caixa, meta: {requireAuth: true, permission: 'caixa'}},
            {
                name: 'fechamento-caixa',
                path: '/relatorios/fechamento-caixa',
                component: FechamentoCaixa,
                meta: {requireAuth: true, permission: 'relatorios'}
            },
            {
                name: 'fechamento-caixa-detalhes',
                path: '/relatorios/fechamento-caixa/detalhes/:id',
                component: Detalhes,
                meta: {requireAuth: true, permission: 'relatorios'}
            },
            {
                name: 'faturamento-periodo',
                path: '/relatorios/faturamento-periodo',
                component: FaturamentoPeriodo,
                meta: {requireAuth: true, permission: 'relatorios'}
            },
            {
                name: 'faturamento-forma-pagamento',
                path: '/relatorios/faturamento-forma-pagamento',
                component: FaturamentoFormaPagamento,
                meta: {requireAuth: true, permission: 'relatorios'}
            },
            {
                name: 'rateio-profissional',
                path: '/relatorios/rateio-profissional',
                component: RateioProfissional,
                meta: {requireAuth: true, permission: 'relatorios'}
            },
            {
                name: 'detalhes-rateio-profissional',
                path: '/relatorios/rateio-profissional/detalhes/:id',
                component: DetalhesRateio,
                meta: {requireAuth: true, permission: 'relatorios'}
            },
            {
                name: 'lista-aniversariantes',
                path: '/relatorios/lista-aniversariantes',
                component: Aniversariantes,
                meta: {requireAuth: true, permission: 'relatorios'}
            },
            {name: 'meus-dados', path: '/meus-dados', component: MeusDados, meta: {requireAuth: true}},
        ]
    }
];

export default routes;
