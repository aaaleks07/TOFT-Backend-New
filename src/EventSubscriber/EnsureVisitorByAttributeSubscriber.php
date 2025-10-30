<?php

namespace App\EventSubscriber;

use App\Attribute\EnsureVisitor;
use App\Entity\Visitor;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Uid\Uuid;

final class EnsureVisitorByAttributeSubscriber implements EventSubscriberInterface
{
    private const COOKIE_NAME = 'visitor_id';
    private const REQ_ATTR_NEW_VISITOR_UUID = '_new_visitor_uuid';
    private const REQ_ATTR_ATTR_EXPIRY      = '_ensure_visitor_expiry';

    public function __construct(private readonly EntityManagerInterface $em) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 0],
            KernelEvents::RESPONSE   => ['onKernelResponse', -100],
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) return;

        // Controller-Callable ermitteln
        $controller = $event->getController();
        // Formate: [object, 'method'] | Closure | invokable object
        if (is_array($controller)) {
            [$object, $method] = $controller;
            $refClass  = new ReflectionClass($object);
            $refMethod = new ReflectionMethod($object, $method);
        } elseif (is_object($controller) && method_exists($controller, '__invoke')) {
            $refClass  = new ReflectionClass($controller);
            $refMethod = new ReflectionMethod($controller, '__invoke');
        } else {
            // Closure o.ä.: hier kein Attribut möglich → abbrechen
            return;
        }

        // Prüfen, ob das Attribut an Klasse ODER Methode hängt
        $attr = $this->getEnsureVisitorAttribute($refClass, $refMethod);
        if (!$attr) return;

        $request = $event->getRequest();
        $cookieVal = $request->cookies->get(self::COOKIE_NAME);

        // Cookie existiert und ist valide? Optional mit DB-Check auf Waisencookie.
        if (!empty($cookieVal) && Uuid::isValid($cookieVal)) {
            $repo = $this->em->getRepository(Visitor::class);
            if ($repo->find($cookieVal)) {
                return; // alles ok, nichts zu tun
            }
        }

        // Neuen Visitor anlegen
        $visitor = new Visitor();
        $this->em->persist($visitor);
        $this->em->flush();

        $uuid = $visitor->getId()->toRfc4122();
        $request->attributes->set(self::REQ_ATTR_NEW_VISITOR_UUID, $uuid);
        $request->cookies->set(self::COOKIE_NAME, $uuid);

        // Ablaufdauer aus Attribut (oder Default)
        $request->attributes->set(self::REQ_ATTR_ATTR_EXPIRY, $attr->expiry ?? '+1 year');
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) return;

        $request  = $event->getRequest();
        $response = $event->getResponse();

        $uuid = $request->attributes->get(self::REQ_ATTR_NEW_VISITOR_UUID);
        if (!$uuid) return;

        $expiry = $request->attributes->get(self::REQ_ATTR_ATTR_EXPIRY) ?? '+1 year';
        $secure = $request->isSecure();

        $cookie = Cookie::create(self::COOKIE_NAME)
            ->withValue($uuid)
            ->withExpires(strtotime($expiry))
            ->withPath('/')
            ->withSecure($secure)           // im Dev ohne HTTPS ggf. false setzen
            ->withHttpOnly(true)
            ->withSameSite(Cookie::SAMESITE_LAX);

        $response->headers->setCookie($cookie);
    }

    private function getEnsureVisitorAttribute(ReflectionClass $refClass, ReflectionMethod $refMethod): ?EnsureVisitor
    {
        // Methode hat Vorrang
        foreach ($refMethod->getAttributes(EnsureVisitor::class) as $a) {
            /** @var EnsureVisitor $inst */
            $inst = $a->newInstance();
            return $inst;
        }
        // sonst Klasse
        foreach ($refClass->getAttributes(EnsureVisitor::class) as $a) {
            /** @var EnsureVisitor $inst */
            $inst = $a->newInstance();
            return $inst;
        }
        return null;
    }
}
