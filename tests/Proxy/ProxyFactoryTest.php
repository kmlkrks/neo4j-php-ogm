<?php

namespace GraphAware\Neo4j\OGM\Tests\Proxy;

use GraphAware\Neo4j\OGM\Proxy\ProxyFactory;
use GraphAware\Neo4j\OGM\Tests\Integration\IntegrationTestCase;
use GraphAware\Neo4j\OGM\Tests\Util\NodeProxy;
use GraphAware\Neo4j\OGM\Proxy\EntityProxy;

class ProxyFactoryTest extends IntegrationTestCase
{
    public function testProxyCreation()
    {
        $cm = $this->em->getClassMetadata(Init::class);
        $factory = new ProxyFactory($this->em, $cm);
        $id = $this->createSmallGraph();
        $o = $factory->fromNode(new NodeProxy($id));

        $this->assertInstanceOf(Init::class, $o);
        $this->assertInstanceOf(EntityProxy::class, $o);
        $this->assertInstanceOf(Related::class, $o->getRelation());
        $this->assertNotNull($o->getRelation()->getId());
    }

    public function testProxyIsReturnedFromRepository()
    {
        $this->em->clear();
        $id = $this->createSmallGraph();

        $init = $this->em->getRepository(Init::class)->findOneById($id);
        $this->assertInstanceOf(Init::class, $init);
        $this->assertInstanceOf(EntityProxy::class, $init);
        $this->assertNotNull($init->getId());
        $this->assertEquals('Ale', $init->getName());
        $this->assertInstanceOf(Related::class, $init->getRelation());
        $this->assertEquals('Chris', $init->getRelation()->getName());
    }

    private function createSmallGraph()
    {
        return $this->client->run('CREATE (n:Init {name:"Ale"})-[:RELATES]->(n2:Related {name:"Chris"}) RETURN id(n) AS id')->firstRecord()->get('id');
    }
}