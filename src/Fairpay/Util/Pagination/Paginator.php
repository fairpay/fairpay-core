<?php


namespace Fairpay\Util\Pagination;


use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

class Paginator
{
    /** @var  Router */
    private $router;

    /**
     * Paginator constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Paginate a QueryBuilder and return a PageView. The p and l query parameters are used.
     * @param QueryBuilder $qb
     * @param Request      $request
     * @return PageView
     */
    public function paginate(QueryBuilder $qb, Request $request)
    {
        $page = max(1, (int) $request->query->get('p', 1));
        $limit = max(1, min(100, (int) $request->query->get('l', 50)));
        $alias = $qb->getRootAliases()[0];

        $qbCount = clone $qb;
        $count = (int) $qbCount->select("COUNT($alias)")->getQuery()->getSingleScalarResult();
        $data = $qb
            ->setMaxResults($limit)
            ->setFirstResult($limit * ($page - 1))
            ->getQuery()
            ->getResult()
        ;

        $pageView = new PageView($count, $limit, $data, $page);

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');
        $pageView->next = $this->router->generate($route, array_merge($routeParams, ['p' => $page + 1, 'l' => $limit]));
        $pageView->prev = $this->router->generate($route, array_merge($routeParams, ['p' => $page - 1, 'l' => $limit]));

        if ($page >= $pageView->totalPages) {
            $pageView->next = false;
        }

        if ($page <= 1) {
            $pageView->prev = false;
        }

        return $pageView;
    }
}